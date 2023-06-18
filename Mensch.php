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
    public function __construct($params = array()) {
        $this->name = $params['name'];
        $this->vorname = $params['vorname'];
        $this->gb_datum = $params['gb_datum'];
        $this->email = $params['email'];
    }
    
    /**
     * Set the value of hash
     *
     * @return  self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
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
    public function doseUserExist($conn){
        $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum, email FROM menschen WHERE name = :name AND vorname = :vorname AND gb_datum = :gb_datum AND email = :email;");
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if($stmt->rowCount() == 0){
            return false;
        }elseif($stmt->rowCount() == 1){
            return true;
        }else{
            exit();
        }



    }
    #$welcher -> 1 mail; 2 name vorname gb datum; 3 mit Mensch ID 
    public function activeOrder($conn, $welcher){
        if($welcher == 1){
            $stmt = $conn->prepare("SELECT id, email FROM menschen WHERE email = :email AND email_verified = 1 ;");
            $stmt->bindParam(':email', $this->email);
        }elseif($welcher == 2){
            $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum FROM menschen WHERE name = :name AND vorname = :vorname AND gb_datum = :gb_datum;");
            $stmt->bindParam(':vorname', $this->vorname);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':gb_datum', $this->gb_datum);
        }elseif($welcher == 3){
            $stmt = $conn->prepare("SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE (status != 'frei' OR status != '') AND (besteller_id = :id OR gast1_id = :id OR gast2_id = :id OR gast3_id = :id OR gast4_id = :id);");
            $stmt->bindParam(':id',$this->id);

        }
        
        $stmt->execute();
        $stmtCheckForActiv = $conn->prepare("SELECT id, mensch_id, status FROM Main WHERE mensch_id = :id AND stats != 'frei;'");
        if($welcher == 3){
            $stmtCheckForActiv = $conn->prepare("SELECT id, mensch_id, reservierung_id status FROM Main WHERE mensch_id = :mensch_id AND reservierung_id = :reservierung_id AND stats != 'frei;'");
        }
        foreach($stmt->fetch(PDO::FETCH_ASSOC) as $row){
            if($welcher == 3){
                $stmtCheckForActiv->bindParam(':reservierung_id', $row['id']);
                $stmtCheckForActiv->bindParam('mensch_id', $this->id);
            }else{
           
            $stmtCheckForActiv->bindParam(':mensch_id', $row['id']);
        }
            $stmtCheckForActiv->execute();
            if($stmtCheckForActiv->rowCount() != 0){
                return true;
            }
        }
        return false;
    }
    public function generateHash($hashseed){
        $this->hash = hash('sha3-512', $this->name . $this->vorname .  $this->gb_datum . $this->email . $hashseed, false);
    }
    public function writeInDB($conn) {
        $stmt = $conn->prepare("INSERT INTO menschen (name, vorname, gb_datum, schule_id, email, hash) VALUES (:name, :vorname, :gb_datum, :schul_id, :email, :hash)");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->bindParam(':schul_id', $this->schul_id);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':hash', $this->hash);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row["id"];
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


}
?>