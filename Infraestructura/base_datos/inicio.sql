-- Crea las tablas e inserta los datos iniciales, este fichero se ejecuta automáticamente al primer arranque del contenedor

 
-- Base de datos LogiTrans S.A.

USE logitrans_db;

-- CREACION DE TABLAS

CREATE TABLE IF NOT EXISTS CLIENTES (
    ID_CLIENTE    INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE_CLI    VARCHAR(30) NOT NULL,
    DIRECCION_CLI VARCHAR(50),
    EMAIL_CLI     VARCHAR(50) UNIQUE,
    TELEFONO_CLI  VARCHAR(15),
    PASSWORD_HASH VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS MERCANCIA (
    ID_MERCANCIA INT AUTO_INCREMENT PRIMARY KEY,
    PESO_MERC    DECIMAL(10,2),
    VOLUMEN_MERC DECIMAL(10,2),
    TIPO_MERC    VARCHAR(30),
    ID_CLIENTE   INT NOT NULL,
    FOREIGN KEY (ID_CLIENTE) REFERENCES CLIENTES(ID_CLIENTE)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS MOVIMIENTO_BODEGA (
    ID_MOVIMIENTO   INT AUTO_INCREMENT PRIMARY KEY,
    FECHA_ENTRADA   DATE NOT NULL,
    FECHA_SALIDA    DATE,
    UBICACION_BODEGA VARCHAR(20),
    ID_MERCANCIA    INT NOT NULL,
    FOREIGN KEY (ID_MERCANCIA) REFERENCES MERCANCIA(ID_MERCANCIA)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS DEPARTAMENTOS (
    ID_DEPT    INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE_DEPT VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS EQUIPOS (
    ID_EQUIPO    INT AUTO_INCREMENT PRIMARY KEY,
    TIPO_EQUIPO  VARCHAR(20),
    MARCA_EQUIPO VARCHAR(30),
    MODELO_EQUIPO VARCHAR(30)
);

CREATE TABLE IF NOT EXISTS PUESTOS_TRABAJO (
    ID_PUESTO    INT AUTO_INCREMENT PRIMARY KEY,
    NOMBRE_PUESTO VARCHAR(30),
    ID_DEPT      INT NOT NULL,
    FOREIGN KEY (ID_DEPT) REFERENCES DEPARTAMENTOS(ID_DEPT)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS EMPLEADOS (
    ID_EMPLEADO   INT AUTO_INCREMENT PRIMARY KEY,
    DNI_EMP       VARCHAR(9) NOT NULL UNIQUE,
    NOMBRE_EMP    VARCHAR(50) NOT NULL,
    APELLIDOS_EMP VARCHAR(50) NOT NULL,
    FECHA_ALTA    DATE,
    TELEFONO_EMP  VARCHAR(15),
    EMAIL_EMP     VARCHAR(50) UNIQUE,
    ID_PUESTO     INT NOT NULL,
    ID_EQUIPO     INT,
    FOREIGN KEY (ID_EQUIPO) REFERENCES EQUIPOS(ID_EQUIPO)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (ID_PUESTO) REFERENCES PUESTOS_TRABAJO(ID_PUESTO)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS VEHICULOS (
    ID_VEHICULO              INT AUTO_INCREMENT PRIMARY KEY,
    MATRICULA_VEHI           VARCHAR(7) NOT NULL UNIQUE,
    MARCA_VEHI               VARCHAR(30),
    MODELO_VEHI              VARCHAR(30),
    CAPACIDAD_VEHI           DECIMAL(10,2) NOT NULL,
    ESTADO_MANTENIMIENTO_VEHI VARCHAR(15)
);

CREATE TABLE IF NOT EXISTS ENVIOS (
    ID_ENVIO     INT AUTO_INCREMENT PRIMARY KEY,
    DESTINO      VARCHAR(30),
    ORIGEN       VARCHAR(30),
    ESTADO_ENVIO VARCHAR(15),
    FECHA_ENVIO  DATE,
    ID_EMPLEADO  INT NOT NULL,
    ID_VEHICULO  INT NOT NULL,
    FOREIGN KEY (ID_EMPLEADO) REFERENCES EMPLEADOS(ID_EMPLEADO)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (ID_VEHICULO) REFERENCES VEHICULOS(ID_VEHICULO)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS DETALLE_ENVIO (
    ID_DETALLE   INT AUTO_INCREMENT PRIMARY KEY,
    CANTIDAD     INT NOT NULL,
    ID_MERCANCIA INT NOT NULL,
    ID_ENVIO     INT NOT NULL,
    FOREIGN KEY (ID_MERCANCIA) REFERENCES MERCANCIA(ID_MERCANCIA)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (ID_ENVIO) REFERENCES ENVIOS(ID_ENVIO)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- ─────────────────────────────────────────────────────────────
-- DATOS INICIALES
-- ─────────────────────────────────────────────────────────────

-- DEPARTAMENTOS (mismo orden que el AD)
INSERT INTO DEPARTAMENTOS (NOMBRE_DEPT) VALUES
('Comercial'),
('Marketing'),
('Administracion'),
('Logistica'),
('RRHH'),
('Informatica'),
('Conductores'),
('Finanzas');

-- ID resultantes:
-- 1=Comercial, 2=Marketing, 3=Administracion, 4=Logistica
-- 5=RRHH, 6=Informatica, 7=Conductores, 8=Finanzas

-- PUESTOS DE TRABAJO
INSERT INTO PUESTOS_TRABAJO (NOMBRE_PUESTO, ID_DEPT) VALUES
('Jefe Comercial', 1),           -- 1
('Vendedor', 1),                 -- 2
('Atencion al Cliente', 1),      -- 3
('Jefe Marketing', 2),           -- 4
('Disenador Grafico', 2),        -- 5
('Comunicaciones', 2),           -- 6
('Gerente', 3),                  -- 7
('Asistente Administrativo', 3), -- 8
('Jefe Logistica', 4),           -- 9
('Mozo de Almacen', 4),          -- 10
('Transportista', 4),            -- 11
('Jefe RRHH', 5),                -- 12
('Capacitacion', 5),             -- 13
('Contratacion', 5),             -- 14
('Desarrollador', 6),            -- 15
('Soporte Tecnico', 6),          -- 16
('Jefe Informatica', 6),         -- 17
('Encargado Redes', 6),          -- 18
('Conductor', 7),                -- 19
('Jefe Conductores', 7),         -- 20
('Contador', 8),                 -- 21
('Jefe Financiero', 8),          -- 22
('Tesorero', 8);                 -- 23

-- EQUIPOS
-- Se elimina ID_EQUIPO del INSERT porque es AUTO_INCREMENT
INSERT INTO EQUIPOS (TIPO_EQUIPO, MARCA_EQUIPO, MODELO_EQUIPO) VALUES
('Portatil', 'Dell',    'Latitude 5520'),
('Portatil', 'HP',      'EliteBook 840'),
('Portatil', 'Lenovo',  'ThinkPad X1'),
('Tablet',   'Samsung', 'Galaxy Tab S8'),
('Tablet',   'Apple',   'iPad Pro'),
('Movil',    'Apple',   'iPhone 14 Pro'),
('Movil',    'Samsung', 'Galaxy S23');

-- VEHICULOS
INSERT INTO VEHICULOS (MATRICULA_VEHI, MARCA_VEHI, MODELO_VEHI, CAPACIDAD_VEHI, ESTADO_MANTENIMIENTO_VEHI) VALUES
('1234ABC', 'Mercedes-Benz', 'Actros 1851',   18000.00, 'Operativo'),
('5678DEF', 'Volvo',         'FH16 750',       20000.00, 'Operativo'),
('9012GHI', 'Scania',        'R450',           19000.00, 'Mantenimiento'),
('3456JKL', 'MAN',           'TGX 18.500',     17500.00, 'Operativo'),
('7890MNO', 'Iveco',         'Stralis 460',    16500.00, 'Operativo'),
('2345PQR', 'DAF',           'XF 480',         19500.00, 'Operativo'),
('6789STU', 'Renault',       'T High 520',     18500.00, 'En revision'),
('0123VWX', 'Mercedes-Benz', 'Actros 1845',    17000.00, 'Operativo');

-- ─────────────────────────────────────────────────────────────
-- EMPLEADOS — extraidos del Active Directory
-- Emails basados en SamAccountName@logitrans.local
-- DNIs ficticios — reemplaza por los reales si los tienes
-- ID_PUESTO asignado por defecto segun departamento
-- Ajusta el ID_PUESTO de cada uno segun su cargo real
-- ─────────────────────────────────────────────────────────────

INSERT INTO EMPLEADOS (DNI_EMP, NOMBRE_EMP, APELLIDOS_EMP, FECHA_ALTA, TELEFONO_EMP, EMAIL_EMP, ID_PUESTO) VALUES

-- COMERCIAL (dept 1) — puestos: 1=Jefe, 2=Vendedor, 3=Atencion
('11111111A', 'Laura',   'Soto',     '2020-01-10', '600100001', 'lsoto2@logitrans.local',    3),
('11111112B', 'Diego',   'Castro',   '2020-03-15', '600100002', 'dcastro@logitrans.local',   1),
('11111113C', 'Ruben',   'Silva',    '2021-06-01', '600100003', 'rsilva@logitrans.local',    3),
('11111114D', 'Paula',   'Rey',      '2021-09-10', '600100004', 'prey2@logitrans.local',     3),
('11111115E', 'Nestor',  'Lorenzo',  '2022-02-20', '600100005', 'nlorenzo@logitrans.local',  2),

-- MARKETING (dept 2) — puestos: 4=Jefe, 5=Disenador, 6=Comunicaciones
('22222221A', 'David',   'Cano',     '2020-01-10', '600200001', 'dcano2@logitrans.local',    6),
('22222222B', 'Julia',   'Duran',    '2020-05-15', '600200002', 'jduran@logitrans.local',    5),
('22222223C', 'Sofia',   'Ruiz',     '2021-03-20', '600200003', 'sruiz@logitrans.local',     6),
('22222224D', 'Hector',  'Barrera',  '2021-07-10', '600200004', 'hbarrera@logitrans.local',  4),
('22222225E', 'Rafa',    'Molina',   '2022-01-15', '600200005', 'rmolina@logitrans.local',   5),

-- ADMINISTRACION (dept 3) — puestos: 7=Gerente, 8=Asistente
('33333331A', 'Ana',     'Gomez',    '2019-01-10', '600300001', 'agomez@logitrans.local',    8),
('33333332B', 'Luis',    'Perez',    '2019-06-15', '600300002', 'lperez@logitrans.local',    7),
('33333333C', 'Maria',   'Lopez',    '2020-02-20', '600300003', 'mlopez@logitrans.local',    8),
('33333334D', 'Pedro',   'Sosa',     '2020-09-10', '600300004', 'psosa@logitrans.local',     7),
('33333335E', 'Laura',   'Rios',     '2021-04-15', '600300005', 'lrios@logitrans.local',     8),

-- LOGISTICA (dept 4) — puestos: 9=Jefe, 10=Mozo, 11=Transportista
('44444441A', 'Marta',   'Ruiz',     '2019-03-10', '600400001', 'mruiz@logitrans.local',     10),
('44444442B', 'Carlos',  'Ramos',    '2020-01-15', '600400002', 'cramos@logitrans.local',   9),
('44444443C', 'Miguel',  'Flores',   '2020-07-20', '600400003', 'mflores@logitrans.local',  11),
('44444444D', 'Raul',    'Navarro',  '2021-02-10', '600400004', 'rnavarro@logitrans.local', 10),
('44444445E', 'Isabel',  'Cano',     '2021-08-15', '600400005', 'icano@logitrans.local',    11),

-- RRHH (dept 5) — puestos: 12=Jefe, 13=Capacitacion, 14=Contratacion
('55555551A', 'Jorge',   'Diaz',     '2019-05-10', '600500001', 'jdiaz@logitrans.local',    14),
('55555552B', 'Lucia',   'Martin',   '2020-02-15', '600500002', 'lmartin@logitrans.local',  12),
('55555553C', 'Paula',   'Mendez',   '2020-08-20', '600500003', 'pmendez@logitrans.local',  13),
('55555554D', 'Andrea',  'Blasco',   '2021-03-10', '600500004', 'ablasco@logitrans.local',  14),
('55555555E', 'Diego',   'Costa',    '2021-09-15', '600500005', 'dcosta@logitrans.local',   13),

-- INFORMATICA (dept 6) — puestos: 17=Jefe, 18=Redes, 16=Soporte, 15=Desarrollador
('66666661A', 'Raul',    'Lopez',    '2018-01-10', '600600001', 'rlopez@logitrans.local',   16),
('66666662B', 'Elena',   'Suarez',   '2018-06-15', '600600002', 'esuarez@logitrans.local',  15),
('66666663C', 'Pablo',   'Rey',      '2019-03-20', '600600003', 'prey@logitrans.local',     17),
('66666664D', 'David',   'Gomez',    '2019-09-10', '600600004', 'dgomez@logitrans.local',   18),
('66666665E', 'Nuria',   'Vidal',    '2020-01-15', '600600005', 'nvidal@logitrans.local',   16),
('66666666F', 'Alfonso', 'Lago',     '2020-07-20', '600600006', 'alago@logitrans.local',    15),
('66666667G', 'Daniela', 'Soto',     '2021-02-10', '600600007', 'dsoto@logitrans.local',    18),
('66666668H', 'Jose',    'Molina',   '2021-08-15', '600600008', 'jmolina@logitrans.local',  15),

-- CONDUCTORES (dept 7) — puestos: 20=Jefe, 19=Conductor
('77777771A', 'Natalia', 'Vega',     '2019-04-10', '600700001', 'nvega@logitrans.local',    19),
('77777772B', 'Mario',   'Blanco',   '2019-10-15', '600700002', 'mblanco@logitrans.local',  19),
('77777773C', 'Sergio',  'Rojas',    '2020-04-20', '600700003', 'srojas@logitrans.local',   19),
('77777774D', 'Ines',    'Campos',   '2020-10-10', '600700004', 'icampos@logitrans.local',  19),
('77777775E', 'Fernando','Bravo',    '2021-04-15', '600700005', 'fbravo@logitrans.local',   20),

-- FINANZAS (dept 8) — puestos: 22=Jefe, 23=Tesorero, 21=Contador
('88888881A', 'Pedro',   'Rivera',   '2018-03-10', '600800001', 'privera@logitrans.local',  21),
('88888882B', 'Sara',    'Lago',     '2018-09-15', '600800002', 'slago@logitrans.local',    22),
('88888883C', 'Andres',  'Cruz',     '2019-05-20', '600800003', 'acruz@logitrans.local',    23),
('88888885E', 'Clara',   'Fernandez','2020-05-15', '600800005', 'cfernandez@logitrans.local',21),
('88888886F', 'Hugo',    'Nieto',    '2020-11-20', '600800006', 'hnieto@logitrans.local',   23);