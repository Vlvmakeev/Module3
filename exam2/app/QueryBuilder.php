<?php
    namespace App;
    
    require '../vendor/autoload.php';

    use Aura\SqlQuery\QueryFactory;

    use PDO;
    
    class QueryBuilder
    {
        private $queryFactory;

        private $pdo;

        public function __construct(){
            $this->queryFactory = new QueryFactory('mysql');
            $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=exam2;charset=utf8', 'root', '');
        }

        public function getAll($table){
            $select = $this->queryFactory->newSelect();
            $select->cols(['*'])
                    ->from($table);
            $sth = $this->pdo->prepare($select->getStatement());

            // bind the values and execute
            $sth->execute($select->getBindValues());
    
            // get the results back as an associative array
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }

        public function getByID($table, $id){
            $select = $this->queryFactory->newSelect();

            $select->cols(['*'])
                ->from($table)
                ->where('id = :id')
                ->bindValue('id', $id);

            // prepare the statement
            $sth = $this->pdo->prepare($select->getStatement());

            // bind the values and execute
            $sth->execute($select->getBindValues());

            // get the results back as an associative array
            $result = $sth->fetch(PDO::FETCH_ASSOC);

            return $result;
        }

        public function insert($table, $data){
            $insert = $this->queryFactory->newInsert();

            $insert
                ->into($table)                   // INTO this table
                ->cols($data);

                $sth = $this->pdo->prepare($insert->getStatement());
                $sth->execute($insert->getBindValues());
        }

        public function update($table, $data, $id){
            $update = $this->queryFactory->newUpdate();

            $update
                ->table($table)                  // update this table
                ->cols($data)
                ->where('id = :id', ['id' => $id])      // bind this value to the condition
                ->bindValue('id', $id);   // bind one value to a placeholder

            // prepare the statement
            $sth = $this->pdo->prepare($update->getStatement());

            // execute with bound values
            $sth->execute($update->getBindValues());
        }

        public function delete($table, $id){
            $delete = $this->queryFactory->newDelete();

            $delete
                ->from($table)                   // FROM this table
                ->where('id = :id')
                ->bindValue('id', $id);

            // prepare the statement
            $sth = $this->pdo->prepare($delete->getStatement());

            // execute with bound values
            $sth->execute($delete->getBindValues());
        }
    }
?>