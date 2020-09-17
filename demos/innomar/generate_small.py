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

MAX_CAPACITY = 50
FISH_FAMILIES = ['cod', 'trout', 'halibut', 'turbot']
COORDS = [
	[4.7625732421875,61.189099411875],[4.796905517578125,61.18396991321753],[4.82471466064453,61.192573759231614],[4.843597412109375,61.18711389852817],[4.85492706298828,61.16708631440347],[4.82025146484375,61.17304626397321],[4.802398681640625,61.157150561980494],
	[4.8044586181640625,61.04632098703952],[4.802398681640625,61.04831536461298],[4.79827880859375,61.05280225537275],[4.794673919677734,61.05147287248099],[4.792270660400391,61.05205448435779],[4.792270660400391,61.04939560007237],[4.797248840332031,61.04997725007063],[4.798450469970703,61.0472350923372],[4.792270660400391,61.0472350923372],[4.788494110107422,61.04598857857738],[4.788494110107422,61.04357851297947],
	[5.019035339355469,61.03202427521959],[5.027618408203125,61.02969629338804],[5.023498535156249,61.02520613162094],[5.023155212402344,61.02021631756964],[5.0159454345703125,61.022212337353764],[5.019035339355469,61.02853223839928],[5.011482238769531,61.02919741790914],[5.0049591064453125,61.02686922860023],[5.000495910644531,61.02204600716721],[5.00598907470703,61.018552871837535],[5.002555847167968,61.014726615739214],
	[4.899559020996094,61.07182293820724],[4.8923492431640625,61.07663859132158],[4.895267486572266,61.08078942859151],[4.893379211425781,61.08369469085848],[4.89166259765625,61.08900648083578],[4.898357391357422,61.09224291568816],[4.906425476074219,61.095644964631596],[4.907970428466797,61.09406845084985],[4.911918640136718,61.09406845084985],[4.913120269775391,61.09572793686492],[4.915180206298828,61.09141309215635],[4.907798767089844,61.08908947048015],[4.912090301513672,61.0877616100516],[4.911746978759766,61.08552071922589],[4.908657073974609,61.083528683055384]
]

def main():
	with open('DATA_small.csv', mode='w') as outfile:
		writer = csv.writer(outfile, delimiter=',', quotechar='"', quoting=csv.QUOTE_MINIMAL)
		writer.writerow(['latitude', 'longitude', 'status', 'volume', 'capacity', 'fish_family'])

		for c in COORDS:
			lat = c[1]; lng = c[0]
			status = random.random() <= 0.95

			if status:
				status_label = 'operational'
				volume = random.randint(0, MAX_CAPACITY)
				capacity = 100 * float(volume) / MAX_CAPACITY
				fish_family = FISH_FAMILIES[ random.randint(0, len(FISH_FAMILIES)-1) ]
			else:
				status_label = 'non-operational'
				volume = 0
				capacity = 0.0
				fish_family = 'none'

			writer.writerow([lat, lng, status_label, volume, capacity, fish_family])

		writer.writerow([COORDS[0][1], COORDS[0][0], '', 0, 0.0, ''])
		writer.writerow([COORDS[0][1], COORDS[0][0], '', MAX_CAPACITY, 100.0, ''])

if __name__ == '__main__':
	main()
