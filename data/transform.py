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


def main():
	folder = '[REPLACE]'

	with open(f'{folder}/raw.csv', 'r') as inpfile, open(f'{folder}/DATA.geojson', 'w') as outfile:
		reader = csv.reader(inpfile, delimiter=',')
		data = { "type": "FeatureCollection", "features": [] }

		for row in reader:
			data['features'].append(
				json.loads(row[0])
			)

		json.dump(data, outfile)


if __name__ == '__main__':
	main()
