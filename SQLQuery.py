# -*- coding: utf-8 -*-

import glob
from os.path import basename
import re
__author__ = "jozef_cipa"


class SQLQuery:
    """
        This class lets you define your SQL queries in separate logic files
        and then comfortably import them in right place in your code.
        Keeps code clean and maintainable.
    """

    _dir = "./sql"

    def __init__(self, sql_part):
        # List of files in directory
        self.sql_files = glob.glob(SQLQuery._dir + "/*.sql")

        # Name of part from sql file
        self.sql_part = sql_part

    @classmethod
    def set_sql_dir(cls, dir_path):
        """ Change default sql directory path """

        cls._dir = dir_path

    @classmethod
    def import_sql(cls, sql_part='*'):
        """ Return new object and set sql part """

        return SQLQuery(sql_part)

    def from_file(self, filename):
        """ Return whole sql string or its part from given filename """

        filename += '.sql'

        if not self._file_exists(filename):
            raise Exception("File " + filename + " doesn't exists in directory" + SQLQuery._dir)

        if self.sql_part == '*':
            return self.get_sql(filename)
        else:
            return self.get_sql_part(self.sql_part, filename)

    def _file_exists(self, filename):
        """ Check if given filename exists in sql directory """

        files = map(lambda file: basename(file), self.sql_files)

        return filename in files

    def _read_file(self, filename):
        """ Return content of file """

        with open(SQLQuery._dir + '/' + filename) as f:
            return f.read()

    def get_sql(self, filename):
        """ Return whole sql string """

        return self._read_file(filename)

    def get_sql_part(self, sql_part_name, filename):
        """ Return sql part from file by sql_part_name """

        sql_file = self.get_sql(filename)

        parts = re.split('--@SQLName(.*)\\n', sql_file)  # return list ['name', 'statement', 'name2', 'statement2']
        parts.pop(0)  # remove first empty string

        parts = map(lambda item: item.strip(), parts)  # remove spaces from start and end

        sql_parts = []

        sql_names = parts[0::2]
        sql_statements = parts[1::2]

        if len(sql_names) is not len(sql_statements):
            raise Exception("Wrongs sql file format")

        for i in range(len(sql_names)):
            sql_parts.append({sql_names[i]: sql_statements[i]})

        for sql_part in sql_parts:
            if sql_part_name in sql_part:
                return sql_part[sql_part_name]
        else:
            raise Exception("SQL part with given name: " + sql_part_name + " doesn't exist in " + filename)
