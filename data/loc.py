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

from shapely.geometry import shape #Point, Polygon

def transform():
	with open('Vindkraftanlegg.geojson', mode='r') as Anlegg,\
		 open('Vindturbiner.geojson', mode='r') as Turbiner,\
		 open('Vindkraftanlegg-Out.geojson', mode='w') as AnleggOut,\
		 open('Vindturbiner-Out.geojson', mode='w') as TurbinerOut:
		anlegg = json.load(Anlegg)
		turbiner = json.load(Turbiner)

		for a in anlegg['features']:
			if a['properties']['idriftDato'] == None:
				m = random.randint(1, 12)
				m = f'0{m}' if len(str(m)) < 2 else str(m)
				d = random.randint(1, 28)
				d = f'0{d}' if len(str(d)) < 2 else str(d)

				a['properties']['idriftDato'] = f'2020{m}{d}'
				a['properties']['driftDato'] = f'2020-{m}-{d} 00:00:00'
			else:
				d = a['properties']['idriftDato']
				a['properties']['driftDato'] = f'{d[0:4]}-{d[4:6]}-{d[6:8]} 00:00:00'

		for t in turbiner['features']:
			p = shape(t['geometry'])
			for a in anlegg['features']:
				if p.within( shape(a['geometry']) ):
					t['properties']['idriftDato'] = a['properties']['idriftDato']
					t['properties']['driftDato'] = a['properties']['driftDato']
					break

		json.dump(anlegg, AnleggOut)
		json.dump(turbiner, TurbinerOut)


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
