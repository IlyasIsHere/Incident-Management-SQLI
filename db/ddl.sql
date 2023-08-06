DROP DATABASE IF EXISTS incidents;
CREATE DATABASE incidents;

USE incidents;

CREATE TABLE utilisateurs (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(50) UNIQUE,
    nom VARCHAR(30),
    prenom VARCHAR(30),
    password VARCHAR(255),
    role ENUM('collaborateur', 'technicien')
) ENGINE=InnoDB;

CREATE TABLE incidents (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date DATETIME NOT NULL,
    id_collaborateur INTEGER NOT NULL,
    id_technicien INTEGER,
    statut ENUM('En attente', 'En cours', 'Traité', 'Fermé'),
    last_message_sender ENUM('collaborateur', 'technicien'),
    last_message_was_seen bool DEFAULT false,
    FOREIGN KEY (id_collaborateur) REFERENCES utilisateurs(id),
    FOREIGN KEY (id_technicien) REFERENCES utilisateurs(id)
) ENGINE=InnoDB;


CREATE TABLE messages (
  id INTEGER AUTO_INCREMENT,
  id_incident INTEGER,
  datetime DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  body TEXT,
  sender ENUM('collaborateur', 'technicien'),
  FOREIGN KEY (id_incident) REFERENCES incidents(id) ON DELETE CASCADE,
  PRIMARY KEY (id)
) ENGINE=InnoDB;


INSERT INTO `utilisateurs` (`email`, `nom`, `prenom`, `password`, `role`) VALUES ('collab1@sqli.com', 'nom1', 'prenom1', '$2y$10$dwJJlXGL5TDhaDgUDAFXN.aZYAUdBg3TPU/Yvjsm03zI/3qDKIrHm', 'collaborateur');
INSERT INTO `utilisateurs` (`email`, `nom`, `prenom`, `password`, `role`) VALUES ('collab2@sqli.com', 'nom3', 'prenom3', '$2y$10$dwJJlXGL5TDhaDgUDAFXN.aZYAUdBg3TPU/Yvjsm03zI/3qDKIrHm', 'collaborateur');
INSERT INTO `utilisateurs` (`email`, `nom`, `prenom`, `password`, `role`) VALUES ('tech1@sqli.com', 'nom2', 'prenom2', '$2y$10$dwJJlXGL5TDhaDgUDAFXN.aZYAUdBg3TPU/Yvjsm03zI/3qDKIrHm', 'technicien');
INSERT INTO `utilisateurs` (`email`, `nom`, `prenom`, `password`, `role`) VALUES ('tech2@sqli.com', 'nom4', 'prenom4', '$2y$10$dwJJlXGL5TDhaDgUDAFXN.aZYAUdBg3TPU/Yvjsm03zI/3qDKIrHm', 'technicien');

INSERT INTO incidents (type, titre, description, date, id_collaborateur, statut)
VALUES
    ("Electricité", "Problème de courant", "La prise électrique ne fonctionne pas.", "2023-07-13", 1, "En attente"),
    ("Réseau", "Perte de connexion", "Impossible d'accéder à Internet.", "2023-07-13", 1, "En attente"),
    ("Hardware", "Écran cassé", "L'écran de l'ordinateur est fissuré.", "2023-07-13", 1, "En attente"),
    ("Software", "Erreur de logiciel", "Le logiciel se bloque lors de son utilisation.", "2023-07-13", 1, "En attente"),
    ("Electricité", "Problème de prise", "La prise électrique du bureau ne fonctionne pas.", "2023-07-13", 2, "En attente"),
    ("Réseau", "Lenteur de connexion", "La vitesse de l'Internet est très lente.", "2023-07-13", 2, "En attente"),
    ("Hardware", "Clavier défectueux", "Certaines touches du clavier ne fonctionnent pas.", "2023-07-13", 2, "En attente"),
    ("Software", "Erreur d'application", "L'application se ferme de manière inattendue.", "2023-07-13", 1, "En attente"),
    ("Hardware", "Souris cassée", "La souris ne répond plus aux mouvements.", "2023-07-13", 1, "En attente"),
    ("Software", "Problème d'installation", "L'installation du logiciel échoue à chaque tentative.", "2023-07-13", 2, "En attente");

