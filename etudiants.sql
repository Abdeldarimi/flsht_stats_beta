USE statistiques;

CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    CODAPO INT NULL DEFAULT NULL,
	 cne VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 Nom VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 Prenom VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 CIN VARCHAR(14) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 DATE_NAIS VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 Sexe VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 Lieux_de_nais VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 NI INT NULL DEFAULT NULL,
	 Inscrit VARCHAR(4) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	 Diplome VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci'
    
);
`etudiants_diplomé`statistiquesetudiants