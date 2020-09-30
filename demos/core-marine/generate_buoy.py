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
import random


lrange = lambda a,b : range(a, b+1)

STATION = [ 5.0693321228027335, 60.09104274189209 ]

def main():
	with open('DATA_buoy.csv', mode='w') as outfile:
		writer = csv.writer(outfile, delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL)
		writer.writerow(['latitude', 'longitude', 'buoy (m)', 'timestamp'])

		h = 12; m = 0
		for i in lrange(0, 100):
			m += 1
			if m >= 60: h += 1; m = 0

			hour = f'0{h}' if len(str(h)) < 2 else f'{h}'
			minute = f'0{m}' if len(str(m)) < 2 else f'{m}'

			buoy = round(random.uniform(0, 0.7), 4) * [-1,1][random.randrange(2)]

			timestamp = f'2020-09-30 {hour}:{minute}:00 +00:00'

			writer.writerow([STATION[1], STATION[0], buoy, timestamp])

		#writer.writerow([COORDS[0][1], COORDS[0][0], '', 0, 0.0, ''])
		#writer.writerow([COORDS[0][1], COORDS[0][0], '', MAX_CAPACITY, 100.0, ''])

if __name__ == '__main__':
	main()
