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
                path: 'solr'
                core: 'demo'
                collection: ~
                username: ~
                password: ~

.. warning::

    | for now, the config node name needs to be equal to the core / collection name in order for things to work

``scheme``
----------
| scheme used for your solr instance

``host``
--------
| hostname or ip address for your solr instance

``port``
--------
| port number for your solr instance

``path``
--------
| path used at your solr instance. used to be ``solr`` nowadays this can be left empty

``core``
--------
| core name this client is to communicate with

``collection``
--------------
| solr cloud collection name this client is to communicate with

.. warning::

    | **never** store un-encrypted (production) authentication data in your repository!
    | `symfony secrets <https://symfony.com/doc/current/configuration/secrets.html>`_ is one of the ways to protect your credentials.

``username``
------------
| username required to authenticate with your solr endpoint

``password``
------------
| password required to authenticate with your solr endpoint

