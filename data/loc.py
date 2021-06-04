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
	with open('Gran - Fremmedarter.csv', mode='r') as In,\
		 open('out.csv', mode='w') as Out:
		reader = csv.reader(In, delimiter=';', quotechar='"')
		writer = csv.writer(Out, delimiter=';', quotechar='"', quoting=csv.QUOTE_MINIMAL)

		writer.writerow(['Institusjon', 'Samling', 'Kategori', 'Vitenskapelig navn', 'Autor', 'Norsk navn', 'Artsgruppe', 'Finner/Samler', 'Funndato', 'Lokalitet', 'Presisjon', 'Kommune', 'Fylke', 'Antall', 'Funnegenskaper', 'Artsbestemt av', 'Validert', 'Katalognummer', 'Latitude', 'Longitude', 'Art rang', 'Aktivitet', 'Uspontan', 'Usikker artsbestemmelse', 'Bildedokumentasjon', 'Ikke funnet', 'Ikke gjennfunnet', 'Identifikasjonsdato', 'Datasettnavn', 'Notater', 'Habitat', 'Kjønn', 'Innsamlingsmetode', 'Intern dataid', 'Felt id', 'Målemetode', 'Georeferanse kommentar', 'Prepareringsmetode', 'Andre Katalognummer', 'Relaterte ressurser', 'Type kobling til ressurs', 'Typestatus', 'Tidspunkt', 'Maks høyde over havet', 'Min høyde over havet', 'Dybde', 'Dynamiske egenskaper', 'Nodeid', 'Institusjonskode', 'Samlingskode'])

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
	with open('map.geojson', mode='r') as fileIn, \
		 open('map2.geojson', mode='w') as fileOut:
		data = json.load(fileIn)

		for f in data['features']:
			curr = { 'United States of America': 'USD', 'United Arab Emirates': 'AED', 'Argentina': 'ARS', 'Australia': 'AUD', 'Bulgaria': 'BGN', 'Brazil': 'BRL', 'Bahamas': 'BSD', 'Canada': 'CAD', 'Switzerland': 'CHF', 'Chile': 'CLP', 'China': 'CNY', 'Colombia': 'COP', 'Czechia': 'CZK', 'Denmark ': 'DKK', 'Dominican Rep.': 'DOP', 'Egypt': 'EGP', 'Finland': 'EUR', 'Estonia': 'EUR', 'Latvia': 'EUR', 'Lithuania': 'EUR', 'Ireland': 'EUR', 'Germany': 'EUR', 'France': 'EUR', 'Netherlands': 'EUR', 'Belgium': 'EUR', 'Luxembourg': 'EUR', 'Austria': 'EUR', 'Slovakia': 'EUR', 'Slovenia': 'EUR', 'Italy': 'EUR', 'Greece': 'EUR', 'Spain': 'EUR', 'Portugal': 'EUR', 'Montenegro': 'EUR', 'Kosovo': 'EUR', 'Fiji': 'FJD', 'United Kingdom': 'GBP', 'Guatemala': 'GTQ', 'Hong Kong': 'HKD', 'Croatia': 'HRK', 'Hungary': 'HUF', 'Indonesia': 'IDR', 'Israel': 'ILS', 'India': 'INR', 'Iceland': 'ISK', 'Japan': 'JPY', 'South Korea': 'KRW', 'Kazakhstan': 'KZT', 'Maldivene': 'MVR', 'Mexico': 'MXN', 'Malaysia': 'MYR', 'Norway': 'NOK', 'New Zealand': 'NZD', 'Panama': 'PAB', 'Peru': 'PEN', 'Philippines': 'PHP', 'Pakistan': 'PKR', 'Poland': 'PLN', 'Paraguay': 'PYG', 'Romania': 'RON', 'Russia': 'RUB', 'Saudi Arabia': 'SAR', 'Sweden': 'SEK', 'Singapore': 'SGD', 'Thailand': 'THB', 'Turkey': 'TRY', 'Taiwan': 'TWD', 'Ukraine': 'UAH', 'Uruguay': 'UYU', 'South Africa': 'ZAR' }

			name = f['properties']['NAME']
			if not name in curr:
				print(name)
				continue

			f['properties']['currency'] = curr[ f['properties']['NAME'] ]
			f['properties']['exchange_rate'] = 0


		json.dump(data, fileOut)


def main():
	transform()
	#database()
	#geojson()


if __name__ == '__main__':
	main()
