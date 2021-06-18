# /*©agpl*************************************************************************
# *                                                                              *
# * Napkin-Visual – High powered map-visualizations                              *
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
import json
import requests
import time
import random
import sqlite3

#from shapely.geometry import shape

def transform():
	with open('Thalictrum minus.csv', mode='r') as In,\
		 open('out.csv', mode='w') as Out:
		reader = csv.reader(In, delimiter=',', quotechar='"')
		writer = csv.writer(Out, delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL)

		writer.writerow(['Institusjon','Samling','Kategori','Vitenskapelig navn','Autor','Norsk navn','Artsgruppe','Finner/Samler','Funndato','Lokalitet','Presisjon','Kommune','Fylke','Antall','Funnegenskaper','Artsbestemt av','Validert','Katalognummer','Latitude','Longitude','Art rang','Aktivitet','Uspontan','Usikker artsbestemmelse','Bildedokumentasjon','Ikke funnet','Ikke gjennfunnet','Endringsdato','Identifikasjonsdato','OccurenceId','Datasettnavn','Notater','Habitat','Kjønn','Innsamlingsmetode','Intern dataid','Felt id','Målemetode','Georeferanse kommentar','Prepareringsmetode','Andre Katalognummer','Relaterte ressurser','Type kobling til ressurs','Typestatus','Tidspunkt','Maks høyde over havet','Min høyde over havet','Dybde','Dynamiske egenskaper','Nodeid','Institusjonskode','Samlingskode'])

		first = True
		for row in reader:
			if first: first = False; continue

			d = row[8].split('.')
			row[8] = f'{d[2]}-{d[1]}-{d[0]} 00:00:00'
			writer.writerow(row)


def database():
	with open('res.csv', mode='r') as fileIn:
		con = sqlite3.connect('locations.db')
		cur = con.cursor()
		rows = csv.reader(fileIn, delimiter='\t')

		cur.executemany("INSERT INTO locations VALUES (?, ?, ?)", rows)

		con.commit()
		con.close()


def geojson():
	with open('Skredhendelse.geojson', mode='r') as fileIn, \
		 open('out.geojson', mode='w') as fileOut:
		data = json.load(fileIn)
		ndata = {"type": "FeatureCollection", "features": []}

		for f in data['features']:
			if f['properties']['totAntPersOmkommet']:
				ndata['features'].append(f)

#			year = dt[0:4]
#			month = dt[4:6]
#			day = dt[6:8]
#			ndt = f'{year}-{month}-{day}'
#
#			if len(dt) > 8:
#				hour = dt[8:10]
#				minute = dt[10:12]
#				second = dt[12:14]
#				ndt = f'{ndt} {hour}:{minute}:{second}'
#			else:
#				ndt = f'{ndt} 00:00:00'
#
#			f['properties']['skredTidspunkt'] = ndt

		json.dump(ndata, fileOut)


def main():
	#transform()
	#database()
	geojson()


if __name__ == '__main__':
	main()
