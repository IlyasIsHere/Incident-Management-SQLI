<?php

require_once $_SERVER["DOCUMENT_ROOT"] . '/src/config/dbconfig.php';

class Incident {

    protected $id;
    protected $type;
    protected $titre;
    protected $description;
    protected $date;
    protected $idCollaborateur;
    protected $idTechnicien;
    protected $statut;
    protected $lastMessageSender;
    protected $lastMessageWasSeen;

    public function getLastMessageSender()
    {
        return $this->lastMessageSender;
    }

    public function getLastMessageWasSeen()
    {
        return $this->lastMessageWasSeen;
    }

    public function __construct($id=null) {
        if (! is_null($id)) {
            $this->id = $id;
        }
    }

    public static function getUserIncidents($user_id)
    {
        global $db;
        $stmt = $db->prepare('SELECT * FROM incidents WHERE id_collaborateur = ?');
        $stmt->execute([$user_id]);

        $arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }

        return $arr;
    }

    public static function getAllIncidents()
    {
        global $db;
        $stmt = $db->prepare('SELECT * FROM incidents');
        $stmt->execute([]);

        $arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }

        return $arr;
    }

    public static function getAssociatedIncidents($tech_id) {
        global $db;

        $stmt = $db->prepare('SELECT * FROM incidents WHERE id_technicien = ?');
        $stmt->execute([$tech_id]);

        $arr = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $arr[] = $row;
        }
        return $arr;
    }

    public function setIncidentInfo() {
        global $db;
        $stmt = $db->prepare('SELECT * FROM incidents WHERE id = ?');
        $stmt->execute([$this->getID()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() != 0) {
            $this->type = $row["type"];
            $this->titre = $row["titre"];
            $this->description = $row["description"];
            $this->date = $row["date"];
            $this->idCollaborateur = $row["id_collaborateur"];
            $this->idTechnicien = $row["id_technicien"];
            $this->statut = $row["statut"];
            $this->lastMessageSender = $row["last_message_sender"];
            $this->lastMessageWasSeen = $row["last_message_was_seen"];

            return true;
        }
        else {
            return false;
        }
    }

    public function getID()
    {
        return $this->id;
    }


    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDate()
    {
        return $this->date;
    }


    public function setDate($date)
    {
        $this->date = $date;
    }


    public function getIdCollaborateur()
    {
        return $this->idCollaborateur;
    }


    public function setIdCollaborateur($id_collaborateur)
    {
        $this->idCollaborateur = $id_collaborateur;
    }


    public function getIdTechnicien()
    {
        return $this->idTechnicien;
    }

    public function setIdTechnicien($id_technicien)
    {
        $this->idTechnicien = $id_technicien;
    }
    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    public function deleteIncident()
    {
        global $db;
        $stmt = $db->prepare('DELETE FROM incidents WHERE id = ?');

        return $stmt->execute([$this->getID()]);

    }

    public static function deleteIncidentByID($incidentID) {
        global $db;
        $stmt = $db->prepare("DELETE FROM incidents WHERE id = ?");

        return $stmt->execute([$incidentID]);
    }

    public function editIncident($titre, $description, $type, $date) {
        global $db;
        $stmt = $db->prepare('UPDATE incidents SET titre = ?, description = ?, type = ?, date = ? WHERE id = ?');

        return $stmt->execute([$titre, $description, $type, $date, $this->getID()]);
    }

    public function confirmIncidentSolved() {
        global $db;
        $stmt = $db->prepare("UPDATE incidents SET statut = 'Fermé' WHERE id = ?");

        return $stmt->execute([$this->getID()]);
    }

    public function associerTechnicien($technicienID) {
        global $db;
        $stmt = $db->prepare("UPDATE incidents SET statut = 'En cours', id_technicien = ? WHERE id = ?");

        return $stmt->execute([$technicienID, $this->getID()]);
    }

    public static function addIncident($user_id, $type, $titre, $date, $description)
    {
        global $db;
        $stmt = $db->prepare("INSERT INTO incidents (type, titre, description, date, id_collaborateur, statut) VALUES (?, ?, ?, ?, ?, 'En attente')");

        return $stmt->execute([$type, $titre, $description, $date, $user_id]);
    }

    public function finirIncident()
    {
        global $db;
        $stmt = $db->prepare("UPDATE incidents SET statut = 'Traité' WHERE id = ?");

        return $stmt->execute([$this->getID()]);
    }

    public function declineIncidentSolved()
    {
        global $db;
        $stmt = $db->prepare("UPDATE incidents SET statut = 'En attente', id_technicien = null WHERE id = ?");

        return $stmt->execute([$this->getID()]);
    }

    public function updateLastMessageSender($sender) {
        global $db;
        $stmt = $db->prepare("UPDATE incidents SET last_message_sender = ?, last_message_was_seen = false WHERE id = ?");

        return $stmt->execute([$sender, $this->getID()]);
    }

    public function declareLastMessageAsSeen() {
        global $db;
        $stmt = $db->prepare("UPDATE incidents SET last_message_was_seen = true WHERE id = ?");

        return $stmt->execute([$this->getID()]);
    }

}

