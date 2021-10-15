APIs
====
| besides providing a means to communicate with your solr instance, this bundle currently provides integration with some of solr's APIs.

| through configuration and several console commands it hopes to take away some the mundane and prone to error tasks.

configuration store
-------------------
| the ``SolrConfigurationStore`` service from this bundle provides access to your serialized configuration. its methods ``getConfigForCore(<core-name>)``, ``getSchemaForCore(<core-name>)`` and ``getParamsForCore(<core-name>)`` will provide you with the appropriate configuration.

.. warning::
    | as there's no way to distinguish which values are set through configuration for the schema api, usage of the config, param and schema api is an all or nothing situation.
    | basically it means you have to implement all configs for the api in order to use it. please be aware of :ref:`config-generate-label` which helps you to generate your config.

supported APIs
--------------

.. toctree::

    apis/coreadmin
    apis/config
    apis/param
    apis/schema