endpoints
=========

.. code-block:: yaml

    # app/config/config.yaml
    solrphp_solarium:
        endpoints:
            default:
                scheme: 'http'
                host: '127.0.0.1'
                port: 8983
                path: solr
                core: demo
                collection: ~
