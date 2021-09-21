endpoints
=========

.. code-block:: yaml

    # app/config/config.yaml
    solrphp_solarium:
        endpoints:
            demo:
                scheme: 'http'
                host: '127.0.0.1'
                port: 8983
                path: solr
                core: demo
                collection: ~

**note**: for now, the config node name needs to be equal to the core / collection name in order for things to work