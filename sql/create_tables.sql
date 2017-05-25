-- Lisää CREATE TABLE lauseet tähän tiedostoon
/*
kysy näiden käytöstä.
CREATE TYPE element AS ENUM ('fire', 'ice', 'shadow');
CREATE TYPE class AS ENUM ('asd', 'wololo', 'tab tab');*/ 

CREATE TABLE Element(
    id SERIAL PRIMARY KEY,
    name character varying(20) NOT NULL UNIQUE,
);

CREATE TABLE Clas(
    id SERIAL PRIMARY KEY,
    name character varying(20) NOT NULL UNIQUE,
);

CREATE TABLE Player(
    id SERIAL PRIMARY KEY,
    name character varying(20) NOT NULL UNIQUE,
    admin boolean NOT NULL
);

CREATE TABLE Avatar(
    id SERIAL PRIMARY KEY,
    p_id INTEGER REFRENCES Player(id),
    e_id INTEGER REFRENCES Element(id),
    c_id INTEGER REFRENCES clas(id),
    name character varying(20) NOT NULL UNIQUE,
    main boolean NOT NULL,
    stats cidr NOT NULL UNIQUE
);

CREATE TABLE Item(
    id SERIAL PRIMARY KEY,   
    name character varying(20) NOT NULL UNIQUE,
);

CREATE TABLE OwnerShip(
    p_id INTEGER REFRENCES Avatar(id),
    i_id INTEGER REFRENCES Item(id),
    owned boolean NOT NULL
);