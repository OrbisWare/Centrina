//i really need to make the tables more efficient
CREATE TABLE ct_loginattempts (
  userid INTEGER UNSIGNED NOT NULL,
  time INTEGER UNSIGNED NOT NULL,
  userip VARCHAR(45) NOT NULL
);

CREATE TABLE ct_users (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(255) NOT NULL,
  email  VARCHAR(255) NOT NULL,
  password VARCHAR(128) NOT NULL,
  salt VARCHAR(128) NOT NULL,
  time INTEGER(11) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE ct_roles (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE ct_permissions (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  desc VARCHAR(50) NOT NULL,

  PRIMARY KEY (id)
);

CREATE TABLE ct_roleperms (
  role_id INTEGER UNSIGNED NOT NULL,
  perm_id INTEGER UNSIGNED NOT NULL,

  FOREIGN KEY (role_id) REFERENCES ct_roles(role_id),
  FOREIGN KEY (perm_id) REFERENCES ct_permissions(perm_id)
);

CREATE TABLE ct_userroles (
  user_id INTEGER UNSIGNED NOT NULL,
  role_id INTEGER UNSIGNED NOT NULL,

  FOREIGN KEY (user_id) REFERENCES ct_users(user_id),
  FOREIGN KEY (role_id) REFERENCES ct_roles(role_id)
);

//Invite Module
CREATE TABLE ct_invites (
  id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  userid INTEGER UNSIGNED NOT NULL,
  code VARCHAR(32) NOT NULL,
  time INTEGER(11) NOT NULL,
  claim INTEGER UNSIGNED NOT NULL,
  taken INTEGER(1) NOT NULL,

  PRIMARY KEY (id)
);
