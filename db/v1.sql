CREATE TABLE users
(
    id       VARCHAR(255) NOT NULL PRIMARY KEY,
    name     VARCHAR(255) NOT NULL,
    email    VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    active   BOOLEAN      NOT NULL DEFAULT FALSE
);

CREATE TABLE folders
(
    id        VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id   VARCHAR(255) NOT NULL,
    parent_id VARCHAR(255),
    name      VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES folders (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE files
(
    id        VARCHAR(255) NOT NULL PRIMARY KEY,
    folder_id VARCHAR(255) NOT NULL,
    title     VARCHAR(255) NOT NULL,
    public    BOOLEAN      NOT NULL DEFAULT FALSE,
    content   TEXT,
    size      BIGINT       NOT NULL DEFAULT 0,
    updated   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (folder_id) REFERENCES folders (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE VIEW folders_view AS
SELECT f.*, p.name AS parent_name, u.name AS user_name
FROM folders f
         LEFT JOIN folders p ON f.parent_id = p.id
         LEFT JOIN users u ON f.user_id = u.id;

CREATE VIEW files_view AS
SELECT f.*, fv.name AS folder_name, fv.user_id, fv.user_name
FROM files f
         LEFT JOIN folders_view fv ON f.folder_id = fv.id;
