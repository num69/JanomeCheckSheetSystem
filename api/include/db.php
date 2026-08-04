<?php

class DbSqlSrv
{
    /**
     * รายการข้อผิดพลาดที่เกิดขึ้นระหว่างการทำงาน
     *
     * @var array
     */
    var $error_log = array();
    /**
     * รายการคำสั่ง SQL และพารามิเตอร์ที่ถูกเรียกใช้งาน
     *
     * @var array
     */
    var $sql_log = array();
    /**
     * Statement ล่าสุดที่กำลังใช้งาน
     *
     * @var resource|null
     */
    var $query_id = null;
    /**
     * จำนวนแถวของผลลัพธ์ล่าสุด
     *
     * @var int
     */
    var $num_rows = 0;
    /**
     * การเชื่อมต่อฐานข้อมูล SQL Server
     *
     * @var resource|null
     */
    var $conn = null;
    /**
     * จำนวนแถวที่ได้รับผลกระทบจากคำสั่งล่าสุด
     *
     * @var int
     */
    var $last_affected_rows = 0;
    /**
     * สถานะการทำงานของ Transaction
     *
     * @var bool
     */
    var $in_transaction = false;

    /**
     * ชื่อหรือที่อยู่ของ SQL Server
     *
     * @var string
     */
    var $serverName = "192.168.100.119,1433";
    /**
     * ค่าการเชื่อมต่อฐานข้อมูล SQL Server
     *
     * @var array
     */
    var $connectionInfo = array(
        "Database" => "Warehouse",
        "UID" => "sa",
        "PWD" => "1234",
        "CharacterSet" => "UTF-8",
        "Encrypt" => false,
        "TrustServerCertificate" => true
    );

    /**
     * สร้างออบเจ็กต์สำหรับจัดการฐานข้อมูล SQL Server
     *
     * @param array $config ค่าการเชื่อมต่อฐานข้อมูลที่ต้องการกำหนดเพิ่มเติม
     * @return void
     */
    function __construct($config = array())
    {
        if (isset($config['serverName'])) {
            $this->serverName = $config['serverName'];
        }

        if (isset($config['connectionInfo']) && is_array($config['connectionInfo'])) {
            $this->connectionInfo = array_merge(
                $this->connectionInfo,
                $config['connectionInfo']
            );
        }
    }

    /**
     * เปิดการเชื่อมต่อฐานข้อมูล SQL Server
     *
     * หากมีการเชื่อมต่ออยู่แล้ว จะคืนค่าการเชื่อมต่อเดิมกลับมา
     *
     * @return resource ทรัพยากรการเชื่อมต่อฐานข้อมูล
     * @throws RuntimeException เมื่อไม่สามารถเชื่อมต่อฐานข้อมูลได้
     */
    function connect()
    {
        if ($this->conn !== null) {
            return $this->conn;
        }

        $this->conn = sqlsrv_connect($this->serverName, $this->connectionInfo);

        if ($this->conn === false) {
            $this->handleError('Database connection failed');
        }

        return $this->conn;
    }

    /**
     * ปิด Statement และการเชื่อมต่อฐานข้อมูล
     *
     * หากมี Transaction ที่ยังไม่สิ้นสุด ระบบจะ Rollback อัตโนมัติ
     *
     * @return void
     */
    function close()
    {
        if ($this->query_id) {
            @sqlsrv_free_stmt($this->query_id);
            $this->query_id = null;
        }

        // ป้องกันข้อมูลค้าง หาก connection ถูกปิดระหว่าง Transaction
        if ($this->conn !== null && $this->in_transaction) {
            @sqlsrv_rollback($this->conn);
            $this->in_transaction = false;
        }

        if ($this->conn !== null) {
            sqlsrv_close($this->conn);
            $this->conn = null;
        }
    }

    /**
     * ปิดการเชื่อมต่อฐานข้อมูลเมื่อออบเจ็กต์ถูกทำลาย
     *
     * @return void
     */
    function __destruct()
    {
        $this->close();
    }

    /**
     * ประมวลผลคำสั่ง SQL แบบใช้พารามิเตอร์
     *
     * @param string $sql คำสั่ง SQL ที่ต้องการประมวลผล
     * @param array $params ค่าพารามิเตอร์สำหรับคำสั่ง SQL
     * @return resource ทรัพยากร Statement ที่ได้จากการประมวลผล
     * @throws RuntimeException เมื่อประมวลผลคำสั่ง SQL ไม่สำเร็จ
     */
    function query($sql, $params = array())
    {
        $this->connect();

        $this->sql_log[] = array(
            'sql' => $sql,
            'params' => $params
        );

        $this->query_id = sqlsrv_query($this->conn, $sql, $params);

        if ($this->query_id === false) {
            $this->handleError('SQL query failed', $sql);
        }

        $affected = sqlsrv_rows_affected($this->query_id);
        $this->last_affected_rows = ($affected === false) ? 0 : intval($affected);

        return $this->query_id;
    }

    /**
     * ดึงข้อมูลทั้งหมดจากคำสั่ง SQL
     *
     * @param string $sql คำสั่ง SQL ที่ต้องการประมวลผล
     * @param array $params ค่าพารามิเตอร์สำหรับคำสั่ง SQL
     * @return array รายการข้อมูลทั้งหมดในรูปแบบ Associative Array
     */
    function fetch_all($sql, $params = array())
    {
        $stmt = $this->query($sql, $params);
        $data = array();

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return $data;
    }

    /**
     * ดึงข้อมูลหนึ่งแถวจากคำสั่ง SQL
     *
     * @param string $sql คำสั่ง SQL ที่ต้องการประมวลผล
     * @param array $params ค่าพารามิเตอร์สำหรับคำสั่ง SQL
     * @return array|null ข้อมูลหนึ่งแถว หรือ null เมื่อไม่พบข้อมูล
     */
    function fetch_one($sql, $params = array())
    {
        $stmt = $this->query($sql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return ($row === false || $row === null) ? null : $row;
    }

    /**
     * ดึงจำนวนข้อมูลจากคอลัมน์ชื่อ total
     *
     * คำสั่ง SQL ต้องกำหนด Alias ของค่าที่นับเป็น total
     *
     * @param string $sql คำสั่ง SQL สำหรับนับจำนวนข้อมูล
     * @param array $params ค่าพารามิเตอร์สำหรับคำสั่ง SQL
     * @return int จำนวนข้อมูลที่พบ
     */
    function count($sql, $params = array())
    {
        $row = $this->fetch_one($sql, $params);
        return isset($row['total']) ? intval($row['total']) : 0;
    }

    /**
     * เพิ่มข้อมูลใหม่หนึ่งแถวลงในตาราง
     *
     * @param string $table ชื่อตารางที่ต้องการเพิ่มข้อมูล
     * @param array $data ข้อมูลแบบชื่อคอลัมน์และค่าที่ต้องการบันทึก
     * @return int จำนวนแถวที่ได้รับผลกระทบ
     * @throws InvalidArgumentException เมื่อข้อมูลสำหรับบันทึกว่างเปล่า
     */
    function insert($table, $data)
    {
        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException('Insert data cannot be empty.');
        }

        $table = $this->quoteIdentifier($table);
        $fields = array();
        $holders = array();
        $params = array();

        foreach ($data as $key => $val) {
            $fields[] = $this->quoteIdentifier($key);
            $holders[] = '?';
            $params[] = ($val === null) ? null : $val;
        }

        $sql = 'INSERT INTO ' . $table
            . ' (' . implode(', ', $fields) . ')'
            . ' VALUES (' . implode(', ', $holders) . ')';

        $stmt = $this->query($sql, $params);
        $affected = $this->last_affected_rows;

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return $affected;
    }

    /**
     * เพิ่มข้อมูลใหม่หนึ่งแถวและคืนค่า IDENTITY ที่สร้างขึ้น
     *
     * เมธอดนี้กำหนดให้คอลัมน์ IDENTITY มีชื่อว่า ID
     *
     * @param string $table ชื่อตารางที่ต้องการเพิ่มข้อมูล
     * @param array $data ข้อมูลแบบชื่อคอลัมน์และค่าที่ต้องการบันทึก
     * @return int|null ค่า IDENTITY ที่สร้างขึ้น หรือ null เมื่อไม่พบค่า
     * @throws InvalidArgumentException เมื่อข้อมูลสำหรับบันทึกว่างเปล่า
     */
    function insert_id($table, $data)
    {
        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException('Insert data cannot be empty.');
        }

        $table = $this->quoteIdentifier($table);
        $fields = array();
        $holders = array();
        $params = array();

        foreach ($data as $key => $val) {
            $fields[] = $this->quoteIdentifier($key);
            $holders[] = '?';
            $params[] = ($val === null) ? null : $val;
        }

        $sql = 'INSERT INTO ' . $table
            . ' (' . implode(', ', $fields) . ')'
            . ' OUTPUT INSERTED.ID'
            . ' VALUES (' . implode(', ', $holders) . ')';

        $stmt = $this->query($sql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_NUMERIC);

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return isset($row[0]) ? intval($row[0]) : null;
    }

    /**
     * แก้ไขข้อมูลตามเงื่อนไขที่กำหนด
     *
     * @param string $table ชื่อตารางที่ต้องการแก้ไขข้อมูล
     * @param array $data ข้อมูลแบบชื่อคอลัมน์และค่าที่ต้องการแก้ไข
     * @param string $where เงื่อนไข WHERE โดยไม่ต้องใส่คำว่า WHERE
     * @param array $whereParams ค่าพารามิเตอร์สำหรับเงื่อนไข WHERE
     * @return int จำนวนแถวที่ได้รับผลกระทบ
     * @throws InvalidArgumentException เมื่อข้อมูลหรือเงื่อนไข WHERE ว่างเปล่า
     */
    function update($table, $data, $where, $whereParams = array())
    {
        if (empty($data) || !is_array($data)) {
            throw new InvalidArgumentException('Update data cannot be empty.');
        }

        if (trim($where) === '') {
            throw new InvalidArgumentException('Update WHERE condition cannot be empty.');
        }

        $table = $this->quoteIdentifier($table);
        $sets = array();
        $params = array();

        foreach ($data as $key => $val) {
            $column = $this->quoteIdentifier($key);

            if ($val === null) {
                $sets[] = $column . ' = NULL';
            } else {
                $sets[] = $column . ' = ?';
                $params[] = $val;
            }
        }

        $sql = 'UPDATE ' . $table
            . ' SET ' . implode(', ', $sets)
            . ' WHERE ' . $where;

        $params = array_merge($params, $whereParams);
        $stmt = $this->query($sql, $params);
        $affected = $this->last_affected_rows;

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return $affected;
    }

    /**
     * ลบข้อมูลตามเงื่อนไขที่กำหนด
     *
     * @param string $table ชื่อตารางที่ต้องการลบข้อมูล
     * @param string $where เงื่อนไข WHERE โดยไม่ต้องใส่คำว่า WHERE
     * @param array $params ค่าพารามิเตอร์สำหรับเงื่อนไข WHERE
     * @return int จำนวนแถวที่ได้รับผลกระทบ
     * @throws InvalidArgumentException เมื่อเงื่อนไข WHERE ว่างเปล่า
     */
    function delete($table, $where, $params = array())
    {
        if (trim($where) === '') {
            throw new InvalidArgumentException('Delete WHERE condition cannot be empty.');
        }

        $table = $this->quoteIdentifier($table);
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $where;

        $stmt = $this->query($sql, $params);
        $affected = $this->last_affected_rows;

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return $affected;
    }

    /**
     * เริ่มต้น Transaction ของฐานข้อมูล
     *
     * @return bool คืนค่า true เมื่อเริ่ม Transaction สำเร็จ
     * @throws RuntimeException เมื่อมี Transaction อยู่แล้วหรือเริ่ม Transaction ไม่สำเร็จ
     */
    function beginTransaction()
    {
        if ($this->in_transaction) {
            throw new RuntimeException('Transaction already started.');
        }

        $this->connect();

        if (!sqlsrv_begin_transaction($this->conn)) {
            $this->handleError('Cannot begin database transaction');
        }

        $this->in_transaction = true;

        return true;
    }

    /**
     * ยืนยันการเปลี่ยนแปลงของ Transaction ปัจจุบัน
     *
     * @return bool คืนค่า true เมื่อ Commit สำเร็จ
     * @throws RuntimeException เมื่อไม่มี Transaction หรือ Commit ไม่สำเร็จ
     */
    function commit()
    {
        if (!$this->in_transaction || $this->conn === null) {
            throw new RuntimeException('No active transaction to commit.');
        }

        if (!sqlsrv_commit($this->conn)) {
            $this->handleError('Cannot commit database transaction');
        }

        $this->in_transaction = false;

        return true;
    }

    /**
     * ยกเลิกการเปลี่ยนแปลงของ Transaction ปัจจุบัน
     *
     * @return bool คืนค่า true เมื่อ Rollback สำเร็จ หรือ false เมื่อไม่มี Transaction
     * @throws RuntimeException เมื่อ Rollback ไม่สำเร็จ
     */
    function rollback()
    {
        if (!$this->in_transaction || $this->conn === null) {
            return false;
        }

        if (!sqlsrv_rollback($this->conn)) {
            $this->handleError('Cannot rollback database transaction');
        }

        $this->in_transaction = false;

        return true;
    }

    /**
     * ตรวจสอบว่าขณะนี้มี Transaction ทำงานอยู่หรือไม่
     *
     * @return bool คืนค่า true เมื่อมี Transaction ทำงานอยู่
     */
    function inTransaction()
    {
        return $this->in_transaction;
    }

    /**
     * คืนจำนวนแถวที่ได้รับผลกระทบจากคำสั่งล่าสุด
     *
     * @return int จำนวนแถวที่ได้รับผลกระทบ
     */
    function affectedRows()
    {
        return $this->last_affected_rows;
    }

    /**
     * ดึงข้อมูลแบบแบ่งหน้าสำหรับ DataTables Server-side
     *
     * ตัวอย่าง $selectColumns:
     * array(
     *     'p.PID' => 'PID',
     *     'p.PartNo' => 'PartNo',
     *     'p.PartName' => 'PartName'
     * )
     *
     * ค่า $customWhere ต้องใช้ Placeholder เช่น "p.IsActive = ?"
     *
     * @param array $selectColumns คอลัมน์จริงของ SQL และชื่อ Alias ที่ส่งกลับ
     * @param string $fromQuery ชื่อตารางหรือคำสั่ง JOIN โดยไม่ต้องใส่คำว่า FROM
     * @param array $searchColumns คอลัมน์ที่อนุญาตให้ค้นหาด้วย Global Search
     * @param array $requestData ข้อมูล Request ที่ส่งมาจาก DataTables
     * @param string $customWhere เงื่อนไข WHERE เพิ่มเติม
     * @param array $customWhereParams ค่าพารามิเตอร์สำหรับเงื่อนไขเพิ่มเติม
     * @param string $defaultOrder คำสั่งเรียงลำดับเริ่มต้น
     * @return array ข้อมูลตามรูปแบบที่ DataTables ต้องการ
     * @throws InvalidArgumentException เมื่อไม่ได้กำหนดคอลัมน์สำหรับ SELECT
     */
    function pagination(
        $selectColumns,
        $fromQuery,
        $searchColumns,
        $requestData,
        $customWhere = '',
        $customWhereParams = array(),
        $defaultOrder = ''
    ) {
        if (empty($selectColumns) || !is_array($selectColumns)) {
            throw new InvalidArgumentException('selectColumns cannot be empty.');
        }

        $this->connect();

        $customWhere = trim($customWhere);
        $searchValue = '';

        if (isset($requestData['search']['value'])) {
            $searchValue = trim($requestData['search']['value']);
        }

        $searchConditions = array();
        $searchParams = array();

        if ($searchValue !== '') {
            foreach ($searchColumns as $column) {
                $searchConditions[] = 'CAST(' . $column . ' AS NVARCHAR(MAX)) LIKE ?';
                $searchParams[] = '%' . $searchValue . '%';
            }
        }

        $baseConditions = array();
        if ($customWhere !== '') {
            $baseConditions[] = '(' . $customWhere . ')';
        }

        $filteredConditions = $baseConditions;
        if (!empty($searchConditions)) {
            $filteredConditions[] = '(' . implode(' OR ', $searchConditions) . ')';
        }

        $baseWhereSql = empty($baseConditions)
            ? ''
            : ' WHERE ' . implode(' AND ', $baseConditions);

        $filteredWhereSql = empty($filteredConditions)
            ? ''
            : ' WHERE ' . implode(' AND ', $filteredConditions);

        $sqlTotal = 'SELECT COUNT(*) AS total FROM ' . $fromQuery . $baseWhereSql;
        $recordsTotal = $this->executeCount($sqlTotal, $customWhereParams);

        $filteredParams = array_merge($customWhereParams, $searchParams);
        $sqlFiltered = 'SELECT COUNT(*) AS total FROM ' . $fromQuery . $filteredWhereSql;
        $recordsFiltered = $this->executeCount($sqlFiltered, $filteredParams);

        $selectSqlParts = array();
        $orderColumns = array();

        foreach ($selectColumns as $actualColumn => $aliasName) {
            $alias = $this->quoteSimpleIdentifier($aliasName);
            $selectSqlParts[] = $actualColumn . ' AS ' . $alias;
            $orderColumns[] = $actualColumn;
        }

        $orderBy = trim($defaultOrder);
        if ($orderBy === '') {
            $orderBy = $orderColumns[0] . ' ASC';
        }

        if (isset($requestData['order'][0]['column'])) {
            $columnIndex = intval($requestData['order'][0]['column']);
            $direction = 'ASC';

            if (
                isset($requestData['order'][0]['dir'])
                && strtolower($requestData['order'][0]['dir']) === 'desc'
            ) {
                $direction = 'DESC';
            }

            if (isset($orderColumns[$columnIndex])) {
                $orderBy = $orderColumns[$columnIndex] . ' ' . $direction;
            }
        }

        $start = isset($requestData['start']) ? max(0, intval($requestData['start'])) : 0;
        $length = isset($requestData['length']) ? intval($requestData['length']) : 10;

        $sqlData = 'SELECT ' . implode(', ', $selectSqlParts)
            . ' FROM ' . $fromQuery
            . $filteredWhereSql
            . ' ORDER BY ' . $orderBy;

        $dataParams = $filteredParams;

        // DataTables จะส่งค่า -1 เมื่อผู้ใช้เลือกให้แสดงข้อมูลทั้งหมด
        if ($length !== -1) {
            $length = max(1, $length);
            $sqlData .= ' OFFSET ? ROWS FETCH NEXT ? ROWS ONLY';
            $dataParams[] = $start;
            $dataParams[] = $length;
        }

        $stmt = $this->query($sqlData, $dataParams);
        $data = array();

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return array(
            'draw' => isset($requestData['draw']) ? intval($requestData['draw']) : 1,
            'recordsTotal' => intval($recordsTotal),
            'recordsFiltered' => intval($recordsFiltered),
            'data' => $data
        );
    }

    /**
     * ประมวลผลคำสั่งนับจำนวนข้อมูลและคืนค่าคอลัมน์ total
     *
     * @param string $sql คำสั่ง SQL สำหรับนับจำนวนข้อมูล
     * @param array $params ค่าพารามิเตอร์สำหรับคำสั่ง SQL
     * @return int จำนวนข้อมูลที่พบ
     */
    function executeCount($sql, $params = array())
    {
        $stmt = $this->query($sql, $params);
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        return isset($row['total']) ? intval($row['total']) : 0;
    }

    /**
     * ตรวจสอบและครอบชื่อตารางหรือชื่อคอลัมน์ด้วยวงเล็บเหลี่ยม
     *
     * รองรับชื่อแบบ dbo.MC_PART หรือ PartNo
     *
     * @param string $identifier ชื่อตารางหรือชื่อคอลัมน์
     * @return string ชื่อที่ผ่านการตรวจสอบและครอบด้วยวงเล็บเหลี่ยม
     * @throws InvalidArgumentException เมื่อรูปแบบชื่อไม่ถูกต้อง
     */
    function quoteIdentifier($identifier)
    {
        $parts = explode('.', $identifier);
        $quoted = array();

        foreach ($parts as $part) {
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $part)) {
                throw new InvalidArgumentException('Invalid SQL identifier: ' . $identifier);
            }

            $quoted[] = '[' . $part . ']';
        }

        return implode('.', $quoted);
    }

    /**
     * ตรวจสอบและครอบชื่อ Alias ด้วยวงเล็บเหลี่ยม
     *
     * @param string $identifier ชื่อ Alias ที่ต้องการตรวจสอบ
     * @return string ชื่อ Alias ที่ผ่านการตรวจสอบและครอบด้วยวงเล็บเหลี่ยม
     * @throws InvalidArgumentException เมื่อรูปแบบชื่อ Alias ไม่ถูกต้อง
     */
    function quoteSimpleIdentifier($identifier)
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Invalid SQL alias: ' . $identifier);
        }

        return '[' . $identifier . ']';
    }

    /**
     * บันทึกรายละเอียดข้อผิดพลาดของ SQL Server และโยน Exception
     *
     * @param string $message ข้อความอธิบายข้อผิดพลาด
     * @param string $sql คำสั่ง SQL ที่เกิดข้อผิดพลาด
     * @return void
     * @throws RuntimeException ทุกครั้งเมื่อถูกเรียกใช้งาน
     */
    function handleError($message, $sql = '')
    {
        $errors = sqlsrv_errors(SQLSRV_ERR_ALL);
        $this->error_log[] = array(
            'message' => $message,
            'sql' => $sql,
            'errors' => $errors
        );

        $details = array();
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $details[] = isset($error['message']) ? $error['message'] : 'Unknown SQL Server error';
            }
        }

        throw new RuntimeException(
            $message . (empty($details) ? '' : ': ' . implode(' | ', $details))
        );
    }

    /**
     * Pagination สำหรับ Raw SQL
     *
     * @param string $sql SQL หลัก โดยไม่ใส่ ORDER BY / OFFSET / FETCH
     * @param array $params Parameter ของ SQL หลัก
     * @param int $page หน้าปัจจุบัน เริ่มจาก 1
     * @param int $pageSize จำนวนข้อมูลต่อหน้า
     * @param string $orderBy ชื่อคอลัมน์จากผลลัพธ์ เช่น "Lot_no ASC, FGID ASC"
     *
     * @return array
     */
    public function paginateSql(
        $sql,
        $params = array(),
        $page = 1,
        $pageSize = 10,
        $orderBy = ''
    ) {
        $this->connect();

        $sql = trim($sql);
        $sql = rtrim($sql, "; \t\n\r\0\x0B");

        $page = max(1, intval($page));
        $pageSize = intval($pageSize);

        if ($pageSize < 1) {
            $pageSize = 10;
        }

        if ($pageSize > 100) {
            $pageSize = 100;
        }

        if (trim($orderBy) === '') {
            throw new InvalidArgumentException(
                'orderBy is required for pagination.'
            );
        }

        $offset = ($page - 1) * $pageSize;

        $countSql = ' SELECT COUNT(*) AS total FROM ( ' . $sql . ') AS CountData';

        $totalRows = $this->executeCount($countSql, $params);
        $totalRows = intval($totalRows);

        $totalPages = $totalRows > 0
            ? intval(ceil($totalRows / $pageSize))
            : 0;


        if ($totalPages > 0 && $page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $pageSize;
        }

        $dataSql =
            'SELECT * FROM (' . $sql . ') AS PageData ORDER BY ' . $orderBy . ' OFFSET ? ROWS FETCH NEXT ? ROWS ONLY';

        $dataParams = $params;
        $dataParams[] = $offset;
        $dataParams[] = $pageSize;

        $stmt = $this->query($dataSql, $dataParams);

        $data = array();

        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $data[] = $row;
        }

        sqlsrv_free_stmt($stmt);
        $this->query_id = null;

        $fromRow = $totalRows > 0 ? $offset + 1 : 0;
        $toRow = min($offset + $pageSize, $totalRows);

        return array(
            'items' => $data,
            'pagination' => array(
                'page' => $page,
                'pageSize' => $pageSize,
                'totalRows' => $totalRows,
                'totalPages' => $totalPages,
                'from' => $fromRow,
                'to' => $toRow,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $totalPages
            )
        );
    }

    /**
     * Pagination สำหรับ SQL ที่มี CTE (Common Table Expression)
     * @param string $cteSql CTE (Common Table Expression) SQL
     * @param string|array $selectColumns คอลัมน์ที่ต้องการเลือก
     * @param string $fromSql ส่วน FROM ของ SQL
     * @param array $params พารามิเตอร์สำหรับ SQL
     * @param int $page หน้าปัจจุบัน
     * @param int $pageSize จำนวนแถวต่อหน้า
     * @param string $orderBy เงื่อนไขการเรียงลำดับ
     * @return array ผลลัพธ์พร้อมข้อมูลการแบ่งหน้า
     */
    public function paginateSqlCte(
        $cteSql,
        $selectColumns,
        $fromSql,
        $params,
        $page,
        $pageSize,
        $orderBy
    ) {
        $page = max(1, (int) $page);
        $pageSize = max(1, (int) $pageSize);
        $offset = ($page - 1) * $pageSize;

        $cteSql = trim($cteSql);
        $fromSql = trim($fromSql);
        $orderBy = trim($orderBy);

        // รองรับทั้ง array และ string
        if (is_array($selectColumns)) {
            $selectColumns = implode(",\n    ", $selectColumns);
        }

        $selectColumns = trim($selectColumns);

        if ($cteSql === '') {
            throw new InvalidArgumentException('cteSql is required.');
        }

        if ($selectColumns === '') {
            throw new InvalidArgumentException('selectColumns is required.');
        }

        if ($fromSql === '') {
            throw new InvalidArgumentException('fromSql is required.');
        }

        if ($orderBy === '') {
            throw new InvalidArgumentException('orderBy is required.');
        }

        if (!is_array($params)) {
            $params = array();
        }

        $countSql = ""
            . $cteSql
            . "SELECT COUNT(*) AS TotalRows "
            . "FROM "
            . "( "
                . "SELECT "
                    . $selectColumns . " "
                . $fromSql
            . ") AS CountData";

        $countResult = $this->fetch_one($countSql, $params);

        $totalRows = 0;

        if (!empty($countResult)) {
            $totalRows = isset($countResult['TotalRows'])
                ? (int) $countResult['TotalRows']
                : 0;
        }

        $dataSql = ""
            . $cteSql
            . "SELECT "
                . $selectColumns . " "
            . $fromSql . " "
            . "ORDER BY "
                . $orderBy . " "
            . "OFFSET ? ROWS "
            . "FETCH NEXT ? ROWS ONLY";

        $dataParams = $params;
        $dataParams[] = $offset;
        $dataParams[] = $pageSize;

        $data = $this->fetch_all($dataSql, $dataParams);

        $totalPages = $totalRows > 0
            ? (int) ceil($totalRows / $pageSize)
            : 0;

        $rowCount = count($data);

        $from = $rowCount > 0
            ? $offset + 1
            : 0;

        $to = $rowCount > 0
            ? $offset + $rowCount
            : 0;

        return array(
            'items' => $data,
            'pagination' => array(
                'page' => $page,
                'pageSize' => $pageSize,
                'totalRows' => $totalRows,
                'totalPages' => $totalPages,
                'from' => $from,
                'to' => $to,
                'hasPrevious' => $page > 1,
                'hasNext' => $page < $totalPages
            )
        );
    }
}
