configs
=========

.. code-block:: yaml

    # app/config/config.yaml
    solrphp_solarium:
        solr_configs:
            -
                cores: ['default']
                search_components:
                    - { name: 'analytics', class: 'org.apache.solr.handler.component.AnalyticsComponent' }
                    - ...
                request_handlers:
                    - { name: '/analytics', class: 'org.apache.solr.handler.AnalyticsHandler' }
                    - name: '/select'
                      class: 'solr.SearchHandler'
                      last_components:
                        - 'analytics'
                    - ...
                query:
                    use_filter_for_sorted_query: false
                    query_result_window_size: 20
                    query_result_max_docs_cached: 200
                    enable_lazy_field_loading: true
                    max_boolean_clauses: 1024
                    filter_cache:
                        autowarm_count: '50%'
                        size: 30000
                        initial_size: 1512
                        class: 'solr.CaffeineCache'
                        name: filterCache
                    query_result_cache: ~
                    document_cache: ~
                    use_circuit_breakers: true
                    memory_circuit_breaker_threshold_pct: 75
                update_handler:
                    class: 'solr.DirectUpdateHandler2'
                    update_log:
                        num_version_buckets: 65536
                    auto_commit:
                        max_time: 15000
                        open_searcher: false

``cores``
---------
| an array of cores the solr configuration applies to.

``search_components``
---------------------
| the `search components <https://solr.apache.org/guide/requesthandlers-and-searchcomponents-in-solrconfig.html#search-components>`_ for your configuration

``request_handlers``
--------------------
| the `request handlers <https://solr.apache.org/guide/requesthandlers-and-searchcomponents-in-solrconfig.html#request-handlers>`_ for your configuration

``query``
---------
| the `query settings <https://solr.apache.org/guide/query-settings-in-solrconfig.html#query-settings-in-solrconfig>`_ for your configuration.

``update_handler``
------------------
| the `update handler settings <https://solr.apache.org/guide/8_9/updatehandlers-in-solrconfig.html>`_ for your configuration