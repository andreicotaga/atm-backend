DROP TABLE IF EXISTS atm.transactions;
DROP TABLE IF EXISTS atm.login_attempts;
DROP TABLE IF EXISTS atm.cards;

CREATE TABLE cards
(
  id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  first_name varchar(100) NOT NULL,
  last_name varchar(100) NOT NULL,
  card_number bigint (16) NOT NULL,
  balance DECIMAL (15,2) NOT NULL,
  blocked bit(1) NOT NULL,
  password varchar (250) NOT NULL,
  updated_at datetime,
  created_at datetime
);

ALTER TABLE atm.cards ADD CONSTRAINT unique_card_id UNIQUE (card_number);

CREATE TABLE transactions
(
  id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  card_id bigint NOT NULL,
  comment varchar(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  oper_code ENUM('DP','WD', 'TF') DEFAULT 'DP',

  FOREIGN KEY (card_id)
  REFERENCES cards(card_number)
    ON DELETE CASCADE
);

CREATE TABLE login_attempts
(
  id bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  card_id bigint NOT NULL,
  attempts INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,

  FOREIGN KEY (card_id)
  REFERENCES cards(id)
    ON DELETE CASCADE
);