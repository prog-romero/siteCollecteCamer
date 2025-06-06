<?php
namespace Phppot;

class DataSource
{
    const HOST = 'localhost';
    const USERNAME = 'romero';
    const PASSWORD = 'romerotchiaze';
    const DATABASENAME = 'db_collecte';
    const PORT = 4306;

    private $conn;

    function __construct()
    {
        $this->conn = $this->getConnection();
    }

    public function getConnection()
    {
        $conn = new \mysqli(self::HOST, self::USERNAME, self::PASSWORD, self::DATABASENAME, self::PORT);

        if (mysqli_connect_errno()) {
            trigger_error("Problem with connecting to database: " . mysqli_connect_error());
        }

        $conn->set_charset("utf8");
        return $conn;
    }

    public function getPdoConnection()
    {
        $conn = FALSE;
        try {
            $dsn = 'mysql:host=' . self::HOST . ';port=' . self::PORT . ';dbname=' . self::DATABASENAME;
            $conn = new \PDO($dsn, self::USERNAME, self::PASSWORD);
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            exit("PDO Connect Error: " . $e->getMessage());
        }
        return $conn;
    }

    public function select($query, $paramType = "", $paramArray = array())
    {
        $stmt = $this->conn->prepare($query);

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $resultset = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $resultset[] = $row;
            }
        }
        return $resultset;
    }

    public function insert($query, $paramType, $paramArray)
    {
        $stmt = $this->conn->prepare($query);
        $this->bindQueryParams($stmt, $paramType, $paramArray);

        $stmt->execute();
        $insertId = $stmt->insert_id;
        return $insertId;
    }

    public function update($query, $paramType, $paramArray)
    {
        $response = array(
            "status" => "error",
            "message" => "something went wrong"
        );
        $stmt = $this->conn->prepare($query);

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        if ($stmt->execute()) {
            $response = array(
                "status" => "success",
                "message" => "Updated Successfully."
            );
        }

        return $response;
    }

    public function execute($query, $paramType = "", $paramArray = array())
    {
        $stmt = $this->conn->prepare($query);

        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
    }

    public function bindQueryParams($stmt, $paramType, $paramArray = array())
    {
        if (!empty($paramType) && !empty($paramArray)) {
            // Vérifier que le nombre de types correspond au nombre de paramètres
            if (strlen($paramType) !== count($paramArray)) {
                trigger_error("Number of param types ($paramType) does not match number of params (" . count($paramArray) . ")", E_USER_ERROR);
            }

            // Créer un tableau pour les paramètres
            $paramValueReference = array_merge([$paramType], array_values($paramArray));

            // Créer des références pour bind_param
            $bindParams = [];
            foreach ($paramValueReference as $key => &$value) {
                $bindParams[$key] = &$value;
            }

            // Appeler bind_param avec les références
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
        }
    }

    public function getRecordCount($query, $paramType = "", $paramArray = array())
    {
        $stmt = $this->conn->prepare($query);
        if (!empty($paramType) && !empty($paramArray)) {
            $this->bindQueryParams($stmt, $paramType, $paramArray);
        }
        $stmt->execute();
        $stmt->store_result();
        $recordCount = $stmt->num_rows;

        return $recordCount;
    }
}
?>