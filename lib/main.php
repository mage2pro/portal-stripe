<?php
/**
 * 2017-06-05
 * The result's format: https://github.com/mage2pro/portal-stripe/blob/0.4.1/etc/customers.json
 * @return array(array(string => string))
 */
function dfe_portal_stripe_customers() {return df_cache_get_simple(__FUNCTION__, function() {
	/** @var array(string => string) $customers */
	$customers = df_map_r(function(array $a) {return [
		$a['id'], $a['country']
	];}, df_oro_get_list('customers', [], [], false, false));
	return array_values(df_map(
		df_sort_names(
			array_filter(
				df_oro_get_list('orders', ['product' => 1], ['website'], false)['included']
				,function(array $a) {return
					'extenddfwebsites' === $a['type']
					&& 'magento_2' === dfa_deep($a, 'relationships/platform/data/id')
				;}
			), null, function(array $a) {return dfa_deep($a, 'attributes/domain');}
		), function(array $a) use($customers) {$at = $a['attributes']; return [
			'country' => $customers[dfa_deep($a, 'relationships/dfcustomer_websites/data/id')]
			,'edition' => $at['m2_is_enterprise']  ? 'Enterprise' : 'Community'
			,'url' => ($u = $at['m2_version_url']) ? df_trim_text_right($u, '/magento_version') :
				"http://{$at['domain']}"
			,'version' => $at['m2_version']
		];
	}));
});}