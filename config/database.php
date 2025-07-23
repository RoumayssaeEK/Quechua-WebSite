<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'quechua';
    private $username = 'root';
    private $password = '';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8", $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function getTotalChansons() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM chansons");
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch(PDOException $e) {
            return 0;
        }
    }

    public function getAllChansons() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM chansons ORDER BY titre_quechua");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getChansonById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM chansons WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return null;
        }
    }

    
    public function getChansonsWithLangue() {
        try {
            $query = "SELECT c.*, l.nom_langue 
                      FROM chansons c 
                      LEFT JOIN langue l ON c.id_langue = l.id_langue 
                      ORDER BY c.titre_quechua";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des chansons avec langue : " . $e->getMessage());
            return [];
        }
    }

    public function getChansonWithLangueById($id) {
        try {
            $query = "SELECT c.*, l.nom_langue,
                         a.nom as nom_auteur,
                         i.nom as nom_interprete
                  FROM chansons c 
                  LEFT JOIN langue l ON c.id_langue = l.id_langue 
                  LEFT JOIN auteur a ON c.id_auteur = a.id_auteur
                  LEFT JOIN interprete i ON c.id_interprete = i.id_interprete
                  WHERE c.id = :id";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la chanson : " . $e->getMessage());
            return null;
        }
    }

    public function getChansonsWithLanguePaginated($limit, $offset) {
    try {
        $query = "SELECT c.*, l.nom_langue 
                  FROM chansons c 
                  LEFT JOIN langue l ON c.id_langue = l.id_langue 
                  ORDER BY c.titre_quechua
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération paginée des chansons : " . $e->getMessage());
        return [];
    }
}

    public function searchChansonsByTitre($search) {
        $stmt = $this->pdo->prepare("
          SELECT * FROM chansons 
          JOIN langue ON chansons.id_langue = langue.id_langue 
          WHERE LOWER(chansons.titre_quechua) LIKE LOWER(:search)
           OR LOWER(chansons.titre_langue) LIKE LOWER(:search)
           ORDER BY chansons.titre_quechua ASC
       ");
        $stmt->execute(['search' => '%' . $search . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getLangues() {
        try {
            $query = "SELECT * FROM langue ORDER BY nom_langue";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des langues : " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les statistiques des chansons
    public function getStatistiques() {
        try {
            $query = "SELECT 
                        COUNT(*) as total_chansons,
                        COUNT(CASE WHEN audio IS NOT NULL AND audio != '' THEN 1 END) as chansons_avec_audio,
                        COUNT(CASE WHEN karaoke IS NOT NULL AND karaoke != '' THEN 1 END) as chansons_avec_karaoke,
                        COUNT(DISTINCT id_langue) as langues_disponibles
                      FROM chansons";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques : " . $e->getMessage());
            return [
                'total_chansons' => 0,
                'chansons_avec_audio' => 0,
                'chansons_avec_karaoke' => 0,
                'langues_disponibles' => 0
            ];
        }
    }

    public function getAllInterpretes() {
    try {
        $stmt = $this->pdo->query("SELECT id_interprete, nom FROM interprete ORDER BY nom");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des interprètes : " . $e->getMessage());
        return [];
    }
}


public function getInterpreteById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM interprete WHERE id_interprete = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function getChansonsByInterprete($id) {
    $stmt = $this->pdo->prepare("
        SELECT c.*, l.nom_langue AS nom_langue
        FROM chansons c
        LEFT JOIN langue l ON c.id_langue = l.id_langue
        WHERE c.id_interprete = ?
        ORDER BY c.titre_quechua ASC
    ");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
?>