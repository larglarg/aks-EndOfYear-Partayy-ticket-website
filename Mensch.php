<?php
class Mensch
{

    protected $name;
    protected $vorname;
    protected $gb_datum;
    protected $email;
    protected $hash;
    protected $schul_id;
    protected $id;
    protected $resverierungID;
    public function __construct($params = array()) {
        $this->name = $params['name'];
        $this->vorname = $params['vorname'];
        $this->gb_datum = $params['gb_datum'];
        $this->email = $params['email'];
    }


    public function setSchul_id($schul_id)
    {
        $this->schul_id = $schul_id;

        return $this;
    }
    public function problemMitInfos($conn)
    #return legend 0-> exestiert nicht in DB 1-> Es exestiert nen user mit 2-> es name, vorname und gb date exestieren 3-> ganz passend 4-> es gibt eine aktive bestellung
    {
        
        $mailExists = false;
        $restExists = false;
        $stmt = $conn->prepare("SELECT id, email FROM menschen WHERE email = :email AND email_verified = 1 ;");
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $mailExists = true;
        }

        $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum FROM menschen WHERE name = :name AND vorname = :vorname AND gb_datum = :gb_datum AND email_verified = 1 ;");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $restExists = true;
            
        }

        if($mailExists && $restExists){
            return 3;
        }elseif($restExists){
            return 2;
        }elseif($mailExists){
            return 1;
        }else{
            return 0;
        }

    }
  /*  public function doseUserExist($conn){
        $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum, email FROM menschen WHERE name = :name AND vorname = :vorname AND gb_datum = :gb_datum AND email = :email AND schul_id = :schul_id;");
        $stmt->bindParam(':vorname', $this->vorname, PDO::PARAM_INT);
        $stmt->bindParam(':name', $this->name, PDO::PARAM_INT);
        $stmt->bindParam(':gb_datum', $this->gb_datum, PDO::PARAM_INT);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_INT);
        $stmt->bindParam(':schul_id', $this->schul_id, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() == 0){
            return false;
        }elseif($stmt->rowCount() == 1){
            return true;
        }else{
            echo "ok wtf who ??";
            exit();
        }



    }
    */
    #typeOfGuast 0-> besteller 1-> gast1 2->gast2 ....
    public function idInBestellung($conn, $typeOfGuast) {
        $columnName = '';
        switch ($typeOfGuast) {
            case 0:
                $columnName = 'besteller_id';
                break;
            case 1:
                $columnName = 'gast1_id';
                break;
            case 2:
                $columnName = 'gast2_id';
                break;
            case 3:
                $columnName = 'gast3_id';
                break;
            case 4:
                $columnName = 'gast4_id';
                break;
        }
    
        $stmt = $conn->prepare("UPDATE bestellung SET $columnName = :id  WHERE id = :reservierung_id;");
        $stmt->bindParam(':reservierung_id', $this->resverierungID, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function userExestiertKomplett($conn){
        $stmt = $conn->prepare("SELECT id, email, name, vorname, gb_datum FROM menschen WHERE email = :email AND name = :name AND vorname = :vorname AND gb_datum = :gb_datum;");
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if($stmt->rowCount() != 0){

            return true;
        }else{
        return false;
    }
    }
    public function SwitchIDtoexistig($conn){
        $stmt = $conn->prepare("SELECT id, email, name, vorname, gb_datum FROM menschen WHERE email = :email AND name = :name AND vorname = :vorname AND gb_datum = :gb_datum;");
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        echo "user is switched to ".$this->id;

    }

    private function checkIDforOrder($conn, $id){
        $stmt = $conn->prepare("SELECT status, mensch_id FROM main WHERE status != 'frei' AND mensch_id = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if($stmt->rowCount() != 0){
            return true;
        }
        return false;
    }
    
    
    #$welcher -> 1 mail; 2 name vorname gb datum; 3 mit Mensch ID 
    public function activeOrder($conn, $welcher){
        switch($welcher){
            case 1:
                $stmt = $conn->prepare('SELECT id, email, email_verified FROM menschen WHERE email = :email AND email_verified = 1;');
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    if($this->checkIDforOrder($conn, $row['id'])){
                        return true;
                    }
                }
            case 2:
                $stmt = $conn->prepare('SELECT id, name, vorname, gb_datum FROM menschen WHERE name = :name AND vorname = :vorname AND gb_datum = :gb_datum; AND email_verified = 1');
                $stmt->bindParam(':vorname', $this->vorname, PDO::PARAM_STR);
                $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':gb_datum', $this->gb_datum, PDO::PARAM_STR);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    if($this->checkIDforOrder($conn, $row['id'])){
                        return true;
                    }
                }
            case 3:
                if($this->checkIDforOrder($conn, $this->id)){
                    return true;
                }

        }
        return false;

    /*    if($welcher == 1){
            $stmt = $conn->prepare("SELECT id, email FROM menschen WHERE email = :email AND email_verified = 1 ;");
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
        }elseif($welcher == 2){
            $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum FROM menschen WHERE name = :name AND vorname = :vorname AND gb_datum = :gb_datum;");
            $stmt->bindParam(':vorname', $this->vorname);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':gb_datum', $this->gb_datum);
            $stmt->execute();
        }elseif($welcher == 3){


        }
        
        
        $stmtCheckForActiv = $conn->prepare("SELECT id, status, mensch_id, status FROM Main WHERE mensch_id = :id AND status != 'frei;'");
        if($welcher == 3){
            $stmtCheckForActiv->bindParam(':id', $this->id);
            $stmtCheckForActiv->execute();
            if($stmtCheckForActiv->rowCount() != 0){
                return true;
            }else{
                return false;
            }
        }else{
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {

           
            $stmtCheckForActiv->bindParam(':id', $row['id']);
            $stmtCheckForActiv->execute();
            if($stmtCheckForActiv->rowCount() != 0){
                return true;
            }

        }
        return false;
        }
        */
        
    }
    public function generateHash($hashseed){
        $this->hash = hash('sha3-512', $this->name . $this->vorname .  $this->gb_datum . $this->email . $hashseed, false);
    }
    public function writeMenschInDB($conn) {
        $stmt = $conn->prepare("INSERT INTO menschen (name, vorname, gb_datum, schule_id, email, hash) VALUES (:name, :vorname, :gb_datum, :schul_id, :email, :hash)");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->bindParam(':schul_id', $this->schul_id);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':hash', $this->hash);
        $stmt->execute();
        
        $this->id = $conn->lastInsertId();
    }
    public function writeIDInMainDB($conn) {
        $stmt = $conn->prepare("UPDATE main SET mensch_id = :id WHERE reservierung_id = :reservierung_id LIMIT 1;");
        $stmt->bindParam(':id', $this->id , PDO::PARAM_INT);
        $stmt->bindParam(':reservierung_id', $this->resverierungID, PDO::PARAM_INT);
        $stmt->execute();
    }

    /*$stmtCheck = $conn->prepare("SELECT id, besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = ':localID' OR gast1_id = ':localID' OR gast2_id = ':localID' OR gast3_id = ':localID' OR gast4_id = ':localID';");
            $stmtMain = $conn->prepare("SELECT Status, mensch_id, reservierung_id WHERE Status != 'frei' AND reservierung_id = :reservierungs_id AND mensch_id = :localID;");
            foreach($stmt->fetch(PDO::FETCH_ASSOC) as $row){
                $stmtCheck->bindParam(':localID', $this->id);
                $stmtCheck->execute();
                $rowCheck = $stmtCheck->fetch(PDO::FETCH_ASSOC);
                $stmtMain->bindParam(':localID', $this->id);
                $stmtMain->bindParam(':reservierung_id', $rowCheck['id']);
                if($stmtMain->rowCount() != 0){
                    $mailExists ;
                }

            }
            */
            /*$stmt = $conn->prepare("SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = ':localID' OR gast1_id = ':localID' OR gast2_id = ':localID' OR gast3_id = ':localID' OR gast4_id = ':localID';");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stmt->bindParam(':localID', $row['id']);
                $stmt->execute();
                if ($stmt->rowCount() != 0) {
                    $status = array("reserviert", "storno", "besteatigt");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        foreach ($status as $currentStatus) {
                            if ($row['status'] == $currentStatus) {
                                return 4;
                            }
                        }
                    }
                }


            }
            */



    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of resverierungID
     *
     * @return  self
     */ 
    public function setResverierungID($resverierungID)
    {
        $this->resverierungID = $resverierungID;

        return $this;
    }

    /**
     * Get the value of hash
     */ 
    public function getHash()
    {
        return $this->hash;
    }
}
?>
