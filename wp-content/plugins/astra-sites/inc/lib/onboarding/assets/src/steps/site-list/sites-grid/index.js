import React from 'react';
import { Grid } from '@brainstormforce/starter-templates-components';
import {
	getDemo,
	checkRequiredPlugins,
	checkFileSystemPermissions,
} from '../../import-site/import-utils';
import { useStateValue } from '../../../store/store';
import { getGridItem } from '../../../utils/functions';

const SiteGrid = ( { sites } ) => {
	const sitesData = sites ? sites : {};
	const storedState = useStateValue();
	const [ { favoriteSiteIDs, currentIndex }, dispatch ] = storedState;

	const allSites = [];

	if ( Object.keys( sitesData ).length ) {
		for ( const siteId in sitesData ) {
			const gridItem = getGridItem( sitesData[ siteId ] );

			allSites.push( gridItem );
		}
	}

	const quickToggleFavorites = ( siteId, favoriteStatus ) => {
		let favoriteIds = favoriteSiteIDs;
		if ( favoriteStatus && ! favoriteIds.includes( siteId ) ) {
			favoriteIds.push( siteId );
		} else {
			favoriteIds = favoriteSiteIDs.filter(
				( existingId ) => existingId !== siteId
			);
		}

		dispatch( {
			type: 'set',
			favoriteSiteIDs: favoriteIds,
		} );
	};

	const toggleFavorites = async ( event, item, favoriteStatus ) => {
		try {
			event.preventDefault();

			const siteId = `id-${ item.id }`;

			// Quick toggle the favorites.
			quickToggleFavorites( siteId, favoriteStatus );

			// Dispatch toggle favorite.
			const formData = new FormData();
			formData.append( 'action', 'astra-sites-favorite' );
			formData.append( 'is_favorite', favoriteStatus );
			formData.append( 'site_id', siteId );
			const resonse = await fetch( ajaxurl, {
				method: 'post',
				body: formData,
			} );
			const data = await resonse.json();

			// Toggle fail so unset favorite.
			if ( ! data.success ) {
				quickToggleFavorites( siteId, false );
			}
		} catch ( err ) {
			// Do nothing
		}
	};

	return (
		<Grid
			column={ 4 }
			options={ allSites }
			hasFavorite
			onFavoriteClick={ toggleFavorites }
			favoriteList={ favoriteSiteIDs }
			onClick={ async ( event, item ) => {
				event.stopPropagation();
				dispatch( {
					type: 'set',
					currentIndex: currentIndex + 1,
					selectedTemplateName: item.title,
					selectedTemplateType: item[ 'astra-sites-type' ],
				} );
				await getDemo( item.id, storedState );
				await checkRequiredPlugins( storedState );
				checkFileSystemPermissions( storedState );
			} }
		/>
	);
};

export default SiteGrid;
