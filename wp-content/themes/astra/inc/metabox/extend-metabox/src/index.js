import { registerPlugin } from '@wordpress/plugins';
import MetaSettings from './settings';

if( astMetaParams.register_astra_metabox ) {
	registerPlugin( 'astra-theme-layout', { render: MetaSettings } );
}
