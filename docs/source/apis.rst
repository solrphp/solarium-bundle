APIs
====
| besides providing a means to communicate with your solr instance, this bundle currently provides integration with some of solr's APIs.

| through configuration and several console commands it hopes to take away some the mundane and prone to error tasks.

configuration store
-------------------
| the ``SolrConfigurationStore`` service from this bundle provides access to your serialized configuration. its methods ``getConfigForCore(<core-name>)`` and ``getSchemaForCore(<core-name>)`` will provide you with the appropriate configuration.

supported APIs
--------------

.. toctree::

    apis/coreadmin
    apis/config
    apis/param
    apis/schema