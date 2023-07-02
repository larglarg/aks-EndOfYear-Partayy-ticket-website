<?php
class Mensch
{

    protected $name;
    protected $vorname;
    protected $gb_datum;
    protected $email;
    protected $hash;
    protected $schul_id;
    protected $id = 0;
    protected $resverierungID;
    public function __construct($params = array())
    {
        if ($params != NULL) {
            $this->name = $params['name'];
            $this->vorname = $params['vorname'];
            $this->gb_datum = $params['gb_datum'];
            $this->email = $params['email'];
        }

    }
    public function loadViaHash($conn, $personHash)
    {
        $stmt = $conn->prepare("SELECT * FROM menschen WHERE hash = :hash");
        $stmt->bindParam(':hash', $personHash, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() != 1) {
            echo "person hash zeigt auf mehr als eine person!";

            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->name = $row['name'];
        $this->vorname = $row['vorname'];
        $this->gb_datum = $row['gb_datum'];
        $this->email = $row['email'];
        $this->hash = $row['name'];
        if (array_key_exists('schul_id', $row)) {
            $this->schul_id = $row['schul_id'];
        }

        $this->id = $row['id'];

        return true;
    }
    public function loadreseRvierungIDViabestellungsHash($conn, $bestellungsHash)
    {
        $stmt = $conn->prepare("SELECT id from bestellung where hash = :hash");
        $stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() != 1) {
            echo "bestellungshash zeigt auf mehr als eine bestellung!";

            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->resverierungID = $row['id'];

        return true;

    }


    public function setSchulIdByName($conn, $schulname)
    {
        $stmt = $conn->prepare("SELECT id, name FROM schulen WHERE name = :Schulname");
        $stmt->bindParam(':Schulname', $schulname, PDO::PARAM_STR);
        $stmt->execute();

        #Schull stuff 
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->schul_id = $row['id'];
                break;
            }
        } else {

            #wenn nein neue schule aufnehmen
            $stmt = $conn->prepare("INSERT INTO schulen (name) VALUES (:schule);");
            $stmt->bindParam(':schule', $schulname, PDO::PARAM_STR);
            $stmt->execute();
            $stmt = $conn->prepare("SELECT id, name FROM schulen WHERE name = :schule;");
            $stmt->bindParam(':schule', $schulname);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->schul_id = $row["id"];
        }
        $stmt = $conn->prepare("UPDATE menschen SET schule_id = :schul_id WHERE id = :id");
        $stmt->bindParam(':schul_id', $this->schul_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function userExestiertKomplett($conn)
    {
        $stmt = $conn->prepare("SELECT id, email, name, vorname, gb_datum FROM menschen WHERE email = :email AND name = :name AND vorname = :vorname AND gb_datum = :gb_datum;");
        $stmt->bindParam(':vorname', $this->vorname);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':gb_datum', $this->gb_datum);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {


            return true;
        } else {

            return false;
        }
    }
    public function problemMitInfos($conn)
    #return legend 0-> exestiert nicht in DB 1-> Es exestiert nen user mit 2-> es name, vorname und gb date exestieren 3-> ganz passend 4-> es gibt eine aktive bestellung
    {

        $mailExists = false;
        $restExists = false;
        $stmt = $conn->prepare("SELECT id, email FROM menschen WHERE email = :email AND email_verified = 1 ;");
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
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

        if ($mailExists && $restExists) {


            return 3;
        } elseif ($restExists) {

            return 2;
        } elseif ($mailExists) {

            return 1;
        } else {

            return 0;
        }

    }
    #typeOfGuast 0-> besteller 1-> gast1 2->gast2 ....
    public function idInBestellung($conn, $typeOfGuast)
    {
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
    public function setResverierungIDviaHash($conn, $bestellungsHash){
        $stmt = $conn->prepare("SELECT id FROM bestellung WHERE hash = :hash");
        $stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
        $stmt->execute();
        $this->id = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
    }

    private function ChangeIDinMain($conn, $newID)
    {
        echo "ChangeIDinMain";
        $stmt = $conn->prepare("UPDATE main SET mensch_id = :newid WHERE mensch_id = :id;");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':newid', $newID, PDO::PARAM_INT);
        $stmt->execute();

    }
    private function ChangeIDinBestellungen($conn, $newID)
    {
        echo "ChangeIDinBestellungen";
        $stmt = $conn->prepare(
            "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id 
                        FROM bestellung 
                        WHERE id = :reservierungs_id;"
        );

        $stmt->bindParam(':reservierungs_id', $this->resverierungID, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        switch ($this->id) {

            case $row['besteller_id']:
                $columnName = 'besteller_id';
            case $row['gast1_id']:
                $columnName = 'gast1_id';
            case $row['gast2_id']:
                $columnName = 'gast2_id';
            case $row['gast3_id']:
                $columnName = 'gast3_id';
            case $row['gast4_id']:
                $columnName = 'gast4_id';

        }
        $stmt = $conn->prepare("UPDATE bestellung SET $columnName = :id  WHERE id = :reservierung_id;");
        $stmt->bindParam(':reservierung_id', $this->resverierungID, PDO::PARAM_INT);
        $stmt->bindParam(':id', $newID, PDO::PARAM_INT);
        $stmt->execute();

    }

    public function SwitchIDtoexistig($conn)
    {
        $stmt = $conn->prepare("SELECT id, email, name, vorname, gb_datum FROM menschen WHERE email = :email AND name = :name AND vorname = :vorname AND  gb_datum = :gb_datum;");
        $stmt->bindParam(':vorname', $this->vorname, PDO::PARAM_STR);
        $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindParam(':gb_datum', $this->gb_datum, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $newID = $row['id'];
            # brauch ich gleube nicht da noch nicht angelegt 
            # $stmt = $conn->prepare('DELETE FROM menschen WHERE id = :id;');
            # $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            # $stmt->execute();
            if ($this->id != 0) {
                $this->ChangeIDinBestellungen($conn, $newID);
                $this->ChangeIDinMain($conn, $newID);
            }
            $this->id = $newID;

        } else { #wenn rowcount nciht eins ist ist was schiefgegeangen 
            echo "error in mensch.SwitchIDtoexistig ";
        }
    }

    private function checkIDforOrder($conn, $id)
    {
        $stmt = $conn->prepare("SELECT status, mensch_id FROM main WHERE status != 'frei' AND mensch_id = :id;");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() != 0) {

            return true;
        }

        return false;
    }

    public function updateMenschviaHash($conn, $hash)
    {
        $stmt = $conn->prepare("UPDATE menschen SET vorname = :vorname, name = :name, gb_datum = :gb_datum, email = :email, schule_id = :schul_id WHERE hash = :hash;");
        $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
        $stmt->bindParam(':vorname', $this->vorname, PDO::PARAM_STR);
        $stmt->bindParam(':schul_id', $this->schul_id, PDO::PARAM_INT);
        $stmt->bindParam(':gb_datum', $this->gb_datum, PDO::PARAM_STR);
        $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
        $stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
        $stmt->execute();



    }
    public function besteatigInMain($conn, $reservierung_id)
    {
        $stmt = $conn->prepare("UPDATE main Set status = 'besteatigt' WHERE mensch_id = :id AND reservierung_id = :resID AND status = 'reserviert';");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':resID', $reservierung_id, PDO::PARAM_STR);
        $stmt->execute();
    }


    #$welcher -> 1 mail; 2 name vorname gb datum; 3 mit Mensch ID 
    public function activeOrder($conn, $welcher)
    {
        switch ($welcher) {
            case 1:
                $stmt = $conn->prepare('SELECT id, email, email_verified FROM menschen WHERE email = :email AND email_verified = 1;');
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    if (!$this->checkIDforOrder($conn, $row['id'])) {

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
                    if (!$this->checkIDforOrder($conn, $row['id'])) {

                        return true;
                    }
                }
            case 3:
                if ($this->checkIDforOrder($conn, $this->id)) {

                    return true;
                }

        }

        return false;

    }
    public function generateHash($hashseed)
    {
        $this->hash = hash('sha3-512', $this->name . $this->vorname . $this->gb_datum . $this->email . $hashseed, false);
    }
    public function writeMenschInDB($conn)
    {
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
    public function verifieMailinDB($conn)
    {
        $stmt = $conn->prepare("UPDATE menschen SET email_verified = 1 WHERE hash = :personHash");
        $stmt->bindParam(":personHash", $this->hash, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function writeIDInMainDB($conn)
    {
        $stmt = $conn->prepare("UPDATE main SET mensch_id = :id WHERE reservierung_id = :reservierung_id LIMIT 1;");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':reservierung_id', $this->resverierungID, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function SetHashFromDB($conn)
    {
        $stmt = $conn->prepare("SELECT id, hash FROM menschen WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->hash = $row['hash'];

    }
    public function setIDviaHash($conn, $personHash){

        $stmt = $conn->prepare("SELECT id, hash FROM menschen WHERE hash = :hash");
        $stmt->bindParam(':hash', $personHash, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->id = $row['id'];
        
    }
    public function getId()
    {

        return $this->id;
    }


    public function setResverierungID($resverierungID)
    {
        $this->resverierungID = $resverierungID;


        return $this;
    }


    public function getHash()
    {

        return $this->hash;
    }


    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }
}
?>