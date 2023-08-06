<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/src/config/dbconfig.php';

class Message {
    protected $body;
    protected $id_incident;
    protected $datetime;
    protected $sender;


    public function getSender()
    {
        return $this->sender;
    }

    public function __construct($body, $id_incident, $sender) {
        $this->body = $body;
        $this->id_incident = $id_incident;
        $this->sender = $sender;
    }

    public function writeMsgToDB() {
        global $db;
        $stmt = $db->prepare("INSERT INTO messages (id_incident, body, sender) VALUES (?, ?, ?)");
        return $stmt->execute([$this->getIdIncident(), $this->getBody(), $this->getSender()]);
    }

    public static function getAllMsgsByIncidentID($incidentID) {
        global $db;
        $result_arr = array();
        $stmt = $db->prepare("SELECT * FROM messages WHERE id_incident = ? ORDER BY datetime");
        $stmt->execute([$incidentID]);

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $msg = new Message($row['body'], $row['id_incident'], $row['sender']);
            $result_arr[] = $msg;
        }

        return $result_arr;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getIdIncident()
    {
        return $this->id_incident;
    }



}