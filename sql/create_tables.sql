-- Lisää CREATE TABLE lauseet tähän tiedostoon

--kysy näiden käytöstä.
--CREATE TYPE element AS ENUM ('fire', 'ice', 'shadow');
--CREATE TYPE class AS ENUM ('asd', 'wololo', 'tab tab');

CREATE TABLE Element(
    id SERIAL PRIMARY KEY,
    type character varying(20) NOT NULL UNIQUE
);

CREATE TABLE Clas(
    id SERIAL PRIMARY KEY,
    name character varying(20) NOT NULL UNIQUE
);

CREATE TABLE Player(
    id SERIAL PRIMARY KEY,
    name character varying(20) NOT NULL UNIQUE,
    password character varying(20) NOT NULL UNIQUE,
    admin boolean NOT NULL
);

CREATE TABLE Avatar(
    id SERIAL PRIMARY KEY,
    p_id INTEGER REFERENCES Player(id) ON DELETE CASCADE NOT NULL,
    e_id INTEGER REFERENCES Element(id) ON DELETE CASCADE NOT NULL,
    c_id INTEGER REFERENCES clas(id) NOT NULL,
    name character varying(20) NOT NULL UNIQUE,
    main boolean NOT NULL,
    stats cidr 
--/*NOT NULL UNIQUE  pitää tutkia enemmän lähdettä

);

CREATE TABLE Item(
    id SERIAL PRIMARY KEY,   
    name character varying(20) NOT NULL UNIQUE
);

CREATE TABLE OwnerShip(
    a_id INTEGER REFERENCES Avatar(id) ON DELETE CASCADE,
    i_id INTEGER REFERENCES Item(id) ON DELETE CASCADE,
    PRIMARY KEY(a_id, i_id)
);