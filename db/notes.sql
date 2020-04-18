--  add FOREIGN KEY
    ALTER TABLE tbl_name
    ADD [CONSTRAINT [symbol]] FOREIGN KEY
    [index_name] (col_name, ...)
    REFERENCES tbl_name (col_name,...)
    [ON DELETE reference_option]
    [ON UPDATE reference_option]

    ALTER TABLE items
    ADD CONSTRAINT  cat_fk
    FOREIGN KEY (cat_id)
    REFERENCES categories(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;