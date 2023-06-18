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
        $this->vorname = $params['Vorname'];
        $this->gb_datum = $params['geburztag'];
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
        $isMail = 0;
        $stmt = $conn->prepare("SELECT id, email FROM menschen WHERE email = ':email';");
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $mailExists = true;
            $stmt = $conn->prepare("SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = ':localID' OR gast1_id = ':localID' OR gast2_id = ':localID' OR gast3_id = ':localID' OR gast4_id = ':localID';");   
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
        }
        $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum FROM menschen WHERE name = ':name' AND vorname = ':vorname' AND gb_datum = ':gb_datum';");
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':vorname ', $this->vorname);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {
            $restExists = true;
            $stmt = $conn->prepare("SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = ':localID' OR gast1_id = ':localID' OR gast2_id = ':localID' OR gast3_id = ':localID' OR gast4_id = ':localID';");
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
        $stmt = $conn->prepare("SELECT id, name, vorname, gb_datum, email FROM menschen WHERE name = ':name' AND vorname = ':vorname' AND gb_datum = ':gb_datum' AND email = ':email';");
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
    public function activeOrder($conn)
    {
        $stmt = $conn->prepare("SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = ':id' OR gast1_id = ':id' OR gast2_id = ':id' OR gast3_id = ':id' OR gast4_id = ':id';");
        $stmt->bindParam(':id',$this->id);
        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            $status = array("reserviert", "storno", "besteatigt");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                foreach ($status as $currentStatus) {
                    if ($row['status'] == $currentStatus) {
                        return 1;
                    }
                }
            }
        }

        return 0;
        #return 0-> keine bestellung 1 -> es läuft eine bestellung
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
    


}
?>