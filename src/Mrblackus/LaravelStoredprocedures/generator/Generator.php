<?php
/**
 * Created by PhpStorm.
 * User: mathieu
 * Date: 02/06/2014
 * Time: 17:06
 */

namespace Mrblackus\LaravelStoredprocedures;

class Generator
{
    /** @var StoredProcedure[] */
    private $storedProcedures;

    /** @var \Twig_Environment */
    private $twig;

    /** @var \PDO */
    private $pdo;

    /** @var string */
    private $schema;

    /** @var string */
    private $modelSaveDir;

    /**
     * @param \PDO   $pdo
     * @param string $sSchema
     * @param string $sSaveDir
     */
    public function __construct(\PDO $pdo, $sSchema, $sSaveDir)
    {
        $this->pdo          = $pdo;
        $this->schema       = $sSchema;
        $this->modelSaveDir = $sSaveDir;

        $twig_options = array(
            'cache'            => false,
            'autoescape'       => false,
            'strict_variables' => true
        );

        $loader     = new \Twig_Loader_Filesystem(__DIR__ . '/../layout');
        $this->twig = new \Twig_Environment($loader, $twig_options);
        $this->twig->addFilter("removeS", new \Twig_Filter_Function("\\Mrblackus\\LaravelStoredProcedures\\Tools::removeSFromTableName"));
    }

    public function run()
    {
        $tables                 = $this->readTables();
        $this->storedProcedures = $this->readSP($tables);

        $this->writeSPModels();
        return count($this->storedProcedures);
    }

    /**
     * @return Table[]
     */
    private function readTables()
    {
        $pdo = $this->pdo;

        $query = $pdo->prepare("
        SELECT DISTINCT
          table_name
        FROM
          information_schema.tables
        WHERE
          table_schema = :schema
          ");
        $query->bindParam('schema', $this->schema);
        $query->execute();

        $fields = $query->fetchAll();

        /** @var Table[] $tables */
        $tables = array();

        foreach ($fields as $f)
        {
            if (!array_key_exists($f['table_name'], $tables))
                $tables[$f['table_name']] = new Table($f['table_name']);
        }

        return $tables;
    }

    private function readSP(Array $tables)
    {
        $pdo = $this->pdo;

        $query = $pdo->prepare("
        SELECT
          r.routine_name,
          r.type_udt_name AS routine_return_type,
          p.ordinal_position AS parameter_position,
          p.parameter_name,
          p.data_type AS parameter_type,
          p.parameter_mode
        FROM
          information_schema.routines r
          LEFT JOIN information_schema.parameters p ON p.specific_name = r.specific_name
        WHERE
          r.specific_schema = :schema AND
          r.routine_type = 'FUNCTION' AND
          r.routine_name LIKE 'sp_%'
        ORDER BY
          r.routine_name, parameter_position
        ");
        $query->bindParam('schema', $this->schema);

        $query->execute();

        $aRawStoredProcedures = array();
        foreach ($query->fetchAll() as $param)
        {
            if (!array_key_exists($param['routine_name'], $aRawStoredProcedures))
            {
                $aRawStoredProcedures[$param['routine_name']] = array(
                    'name'        => $param['routine_name'],
                    'return_type' => $param['routine_return_type'],
                    'parameters'  => array()
                );
            }
            if (!is_null($param['parameter_position']))
            {
                $aRawStoredProcedures[$param['routine_name']]['parameters'][] = new SPParameter($param['parameter_name'],
                    $param['parameter_type'], $param['parameter_mode'], $param['parameter_position']);
            }
        }

        /** @var StoredProcedure[] $aRawStoredProcedures */
        $aStoredProcedures = array();
        $SPFactory         = new StoredProcedureFactory($tables);
        foreach ($aRawStoredProcedures as $rawSP)
        {
            $aStoredProcedures[] = $SPFactory->getStoredProcedure($rawSP['name'], $rawSP['return_type'], $rawSP['parameters']);
        }

        return $aStoredProcedures;
    }

    private function writeSPModels()
    {
        $saveDir = $this->modelSaveDir;
        foreach ($this->storedProcedures as $sp)
        {
            $fileName = $sp->getClassName() . '.php';

            $file = fopen($saveDir . $fileName, "w");
            fwrite($file, $this->SP_ModelToString($sp));
            fclose($file);
        }
    }

    private function SP_ModelToString(StoredProcedure $sp)
    {
        $variables = array();

        $variables['className']  = $sp->getClassName();
        $variables['name']       = $sp->getName();
        $variables['returnType'] = $sp->getCleanReturnType();

        $protoParams   = '';
        $executeParams = '';
        $aINParameters = $sp->getINParameters();
        foreach ($aINParameters as $p)
        {
            $protoParams .= '$' . $p->getName() . ', ';
            $executeParams .= ':' . $p->getName() . ', ';
        }
        $variables['executeProtoParams'] = substr($protoParams, 0, -2);
        $variables['executeParams']      = substr($executeParams, 0, -2);
        $variables['inParameters']       = $aINParameters;

        $variables['hasAttributes'] = false;
        if ($sp instanceof ScalarStoredProcedure)
        {
            $variables['objectHydratation'] = false;
            $variables['fetchMode']         = '\PDO::FETCH_COLUMN';
        }
        else
        {
            $variables['fetchMode']         = '\PDO::FETCH_ASSOC';
            $variables['objectHydratation'] = true;
            /** @var $sp ISPReturnClass */
            $variables['targetClass'] = $sp->getReturnedClassName();
            /** @var $sp StoredProcedure */

            if ($sp instanceof RecordStoredProcedure)
            {
                $variables['hasAttributes'] = true;
                $variables['attributes']    = $sp->getOUParameters();
            }
        }

        return $this->twig->render('sp_model.php.twig', $variables);
    }
} 