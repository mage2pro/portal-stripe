<?php
/**
 * 2017-06-05
 * The result's format: https://github.com/mage2pro/portal-stripe/blob/0.4.1/etc/customers.json
 * @return array(array(string => string))
 */
function dfe_portal_stripe_customers():array {return df_cache_get_simple(__FUNCTION__, function():array {
	$customers = df_map_r(function(array $a):array {return [
		$a['id'], dfa_deep($a, 'relationships/country/data/id')
	];}, df_oro_get_list('customers')['data']); /** @var array(string => string) $customers */
	return array_values(df_map(
		df_sort_names(
			array_filter(
				df_oro_get_list('orders', ['product' => 1], ['website'])['included']
				,function(array $a) {return
					'extenddfwebsites' === $a['type'] && 'magento_2' === dfa_deep($a, 'relationships/platform/data/id')
				;}
			), '', function(array $a) {return dfa_deep($a, 'attributes/domain');}
		), function(array $a) use($customers) {$at = $a['attributes']; return [
			# 2017-07-13 From now on, country can be defined not only for a customer, but for a website too.
			'country' => dfa_deep($a, 'relationships/country/data/id',
				$customers[dfa_deep($a, 'relationships/dfcustomer_websites/data/id')]
			)
			,'edition' => $at['m2_is_enterprise']  ? 'Enterprise' : 'Community'
			,'url' => ($u = $at['m2_version_url']) ? df_trim_text_right($u, '/magento_version') :
				"http://{$at['domain']}"
			,'version' => $at['m2_version']
		];
	}));
});}