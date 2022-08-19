CREATE TABLE Tiempo(
       id INT PRIMARY KEY AUTO_INCREMENT,
       nombre VARCHAR(10)
);

CREATE TABLE Zonas(
       id INT PRIMARY KEY AUTO_INCREMENT,
       nombre VARCHAR(10)
);

CREATE TABLE Registros(
       id INT PRIMARY KEY AUTO_INCREMENT,
       fecha TIMESTAMP,
       tiempoId INT,
       zonaId INT,
       FOREIGN KEY (tiempoId) REFERENCES Tiempo(id),
       FOREIGN KEY (zonaId) REFERENCES Zonas(id)
);



CREATE VIEW Info AS
SELECT Registros.id as 'id',
       Registros.fecha as 'fecha',
       Tiempo.id as 'tiempoId',
       Tiempo.nombre as 'tiempo',
       Zonas.id as 'zonaId',
       Zonas.nombre as 'zona'
FROM
       Registros
       LEFT JOIN Tiempo ON (Tiempo.id = Registros.tiempoId)
       LEFT JOIN Zonas ON (Zonas.id = Registros.zonaId);
       


INSERT INTO Tiempo (nombre) VALUES ('despejado');
INSERT INTO Tiempo (nombre) VALUES ('nublado'); 

INSERT INTO Zonas (nombre) VALUES ('Norte');
INSERT INTO Zonas (nombre) VALUES ('Centro');
INSERT INTO Zonas (nombre) VALUES ('Centro Sur');
INSERT INTO Zonas (nombre) VALUES ('Sur');
INSERT INTO Zonas (nombre) VALUES ('Austral');

INSERT INTO Registros (fecha, tiempoId, zonaId) VALUES ('2020-08-01','2','1');
INSERT INTO Registros (fecha, tiempoId, zonaId) VALUES ('2020-10-30','1','3');
INSERT INTO Registros (fecha, tiempoId, zonaId) VALUES ('2020-11-10','2','4');

