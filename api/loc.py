# /*©agpl*************************************************************************
# *                                                                              *
# * Napkin Visual – Visualisation platform for the Napkin platform               *
# * Copyright (C) 2020  Napkin AS                                                *
# *                                                                              *
# * This program is free software: you can redistribute it and/or modify         *
# * it under the terms of the GNU Affero General Public License as published by  *
# * the Free Software Foundation, either version 3 of the License, or            *
# * (at your option) any later version.                                          *
# *                                                                              *
# * This program is distributed in the hope that it will be useful,              *
# * but WITHOUT ANY WARRANTY; without even the implied warranty of               *
# * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                 *
# * GNU Affero General Public License for more details.                          *
# *                                                                              *
# * You should have received a copy of the GNU Affero General Public License     *
# * along with this program. If not, see <http://www.gnu.org/licenses/>.         *
# *                                                                              *
# *****************************************************************************©*/

import csv
import sqlite3


def transform(inpFile):
	with open(inpFile, mode='r') as fileIn,\
		 open('res.csv', mode='w') as fileOut:
		reader = csv.reader(fileIn, delimiter='\t')
		writer = csv.writer(fileOut, delimiter='\t', quotechar='"', quoting=csv.QUOTE_MINIMAL)

		for row in reader:
			f = row[1]
			if row[3].strip(): f += f',{row[3]}'
			writer.writerow([ f, float(row[4]), float(row[5]) ])


def database():
	with open('res.csv', mode='r') as fileIn:
		con = sqlite3.connect('locations.db')
		cur = con.cursor()
		rows = csv.reader(fileIn, delimiter='\t')

		cur.executemany("INSERT INTO locations VALUES (?, ?, ?)", rows)

		con.commit()
		con.close()


def main():
	#transform('allCountries.csv')
	#transform('cities15000.csv')
	#database()


if __name__ == '__main__':
	main()
