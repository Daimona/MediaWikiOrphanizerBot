<?php
# Copyright (C) 2018 Valerio Bozzolan
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program. If not, see <https://www.gnu.org/licenses/>.

namespace itwikidelbot;

/**
 * PDCs handler
 */
class PDCs {

	/**
	 * Sort some PDCs by creation date
	 *
	 * @param $pdcs array
	 */
	public static function sortByCreationDate( & $pdcs ) {
		// sort PDCs by start date
		usort( $pdcs, function ( $a, $b ) {
			return $a->getCreationDate() > $b->getCreationDate();
		} );
	}

	/**
	 * Index some PDCs by their type
	 *
	 * @param $pdcs array
	 * @return array
	 */
	public static function indexByType( $pdcs ) {
		$pdcs_by_type = [];
		foreach( $pdcs as $pdc ) {
			$type = $pdc->getType();
			if( ! isset( $pdcs_by_type[ $type ] ) ) {
				$pdcs_by_type[ $type ] = [];
			}
			$pdcs_by_type[ $type ][] = $pdc;
		}
		return $pdcs_by_type;
	}

}
