-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO Element(type) VALUES ('potato');
INSERT INTO Element(type) VALUES ('booom');

INSERT INTO Clas(name) VALUES ('noob');
INSERT INTO Clas(name) VALUES ('wallet');

INSERT INTO Player(name, password, admin) VALUES ('asd', 123, FALSE);
INSERT INTO Avatar(p_id, e_id, c_id, name, main) VALUES(
    (SELECT id FROM Player WHERE name='asd'),
    (SELECT id FROM Element WHERE type='booom'),
    (SELECT id FROM Clas WHERE name='noob'),
    'hajoon',
    TRUE);

INSERT INTO Player(name, password, admin) VALUES ('booom', 321, TRUE);
INSERT INTO Avatar(p_id, e_id, c_id, name, main) VALUES(
    (SELECT id FROM Player WHERE name='booom'),
    (SELECT id FROM Element WHERE type='potato'),
    (SELECT id FROM Clas WHERE name='wallet'),
    'whale',
    TRUE);
INSERT INTO Avatar(p_id, e_id, c_id, name, main) VALUES(
    (SELECT id FROM Player WHERE name='booom'),
    (SELECT id FROM Element WHERE type='potato'),
    (SELECT id FROM Clas WHERE name='wallet'),
    'derp',
    FALSE);

INSERT INTO Item(name) VALUES ('stick');
INSERT INTO Item(name) VALUES ('mega stick');

INSERT INTO Ownership(a_id, i_id) VALUES(
    (SELECT id FROM Avatar WHERE name='whale'),
    (SELECT id FROM Item WHERE name='mega stick'));

INSERT INTO Ownership(a_id, i_id) VALUES(
    (SELECT id FROM Avatar WHERE name='derp'),
    (SELECT id FROM Item WHERE name='stick'));
INSERT INTO Ownership(a_id, i_id) VALUES(
    (SELECT id FROM Avatar WHERE name='derp'),
    (SELECT id FROM Item WHERE name='mega stick'));

INSERT INTO Ownership(a_id, i_id) VALUES(
    (SELECT id FROM Avatar WHERE name='hajoon'),
    (SELECT id FROM Item WHERE name='stick'));

