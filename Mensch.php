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
        foreach ($params as $key => $value) {
            $this->{$key} = $value;
        }
    }
    

    /**
     * Getter && setter 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of vorname
     */
    public function getVorname()
    {
        return $this->vorname;
    }

    /**
     * Set the value of vorname
     *
     * @return  self
     */
    public function setVorname($vorname)
    {
        $this->vorname = $vorname;

        return $this;
    }

    /**
     * Get the value of gb_datum
     */
    public function getGb_datum()
    {
        return $this->gb_datum;
    }

    /**
     * Set the value of gb_datum
     *
     * @return  self
     */
    public function setGb_datum($gb_datum)
    {
        $this->gb_datum = $gb_datum;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of hash
     */
    public function getHash()
    {
        return $this->hash;
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

    /**
     * Get the value of schul_id
     */
    public function getSchul_id()
    {
        return $this->schul_id;
    }

    /**
     * Set the value of schul_id
     *
     * @return  self
     */

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

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
        $sql = "SELECT id, email FROM menschen WHERE email = '" . $this->email . " AND ';";
        $result = $conn->query($sql);
        if ($result->rowCount() != 0) {
            $mailExists = true;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $localID = $row['id'];
                $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = '" . $localID . "' OR gast1_id = '" . $localID . "' OR gast2_id = '" . $localID . "' OR gast3_id = '" . $localID . "' OR gast4_id = '" . $localID . "';";
                $result = $conn->query($sql);
                if ($result->rowCount() != 0) {
                    $status = array("reserviert", "storno", "besteatigt");
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        foreach ($status as $currentStatus) {
                            if ($row['status'] == $currentStatus) {
                                return 4;
                            }
                        }
                    }
                }


            }
        }
        $sql = "SELECT id, name, vorname, gb_datum FROM menschen WHERE name = '" . $this->name . "' AND vorname = '" . $this->vorname . "' AND gb_datum = '" . $this->gb_datum . "'";
        $result = $conn->query($sql);
        if ($result->rowCount() != 0) {
            $restExists = true;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $localID = $row['id'];
                $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = '" . $localID . "' OR gast1_id = '" . $localID . "' OR gast2_id = '" . $localID . "' OR gast3_id = '" . $localID . "' OR gast4_id = '" . $localID . "';";
                $result = $conn->query($sql);
                if ($result->rowCount() != 0) {
                    $status = array("reserviert", "storno", "besteatigt");
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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
        $sql = "SELECT id, name, vorname, gb_datum FROM menschen WHERE name = '" . $this->name . "' AND vorname = '" . $this->vorname . "' AND gb_datum = '" . $this->gb_datum . "' AND email = '". $this->email ."';";
        $result = $conn->query($sql);



    }
    public function activeOrder($conn)
    {
        $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = '" . $this->id . "' OR gast1_id = '" . $this->id . "' OR gast2_id = '" . $this->id . "' OR gast3_id = '" . $this->id . "' OR gast4_id = '" . $this->id . "';";
        $result = $conn->query($sql);

        if ($result->rowCount() != 0) {
            $status = array("reserviert", "storno", "besteatigt");
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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

    public function writeInDB($conn){
                #erstellen von menschen in db
                $sql = "INSERT INTO menschen (name, vorname, gb_datum, schule_id, email, hash) VALUES ('" . $this->name . "','" . $this->vorname . "','" . $this->gb_datum . "','" . $this->schul_id . "', '" . $this->email . "','" . $this->personHash . "');";
                $conn->query($sql);
                $sql = "SELECT id, name, vorname, gb_datum, email FROM menschen WHERE name = '" . $this->name . "' AND vorname = '" . $this->vorname . "' AND gb_datum = '" . $this->gb_datum . "' AND email = '" . $this->email . "'";
                $result = $conn->query($sql);
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $this->id = $row["id"];

    }


}
?>