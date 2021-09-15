clients
=========

.. code-block:: yaml

    # app/config/config.yaml
    solrphp_solarium:
        default_client: 'default'
        clients:
            default:
                endpoints: ['default', 'second']
                default_endpoint: 'default'
                client_class: 'My\Custom\Client'
                adapter_class: 'My\Custom\Adapter'
                adapter_service: 'adapter.service'
                dispatcher_service: 'dispatcher.service'

``endpoints``
-------------
the endpoints exposed to your client

``default_endpoint``
--------------------
the default endpoint for your client

``client_class``
----------------
your implementation of ``Solarium\Core\Client\ClientInterface``

defaults to ``Solarium\Core\Client\Client``

``adapter_class``
-----------------
your implementation of ``Solarium\Core\Client\Adapter\AdapterInterface``

defaults to ``Solarium\Core\Client\Adapter\Curl``

``adapter_service``
-------------------
service id of your implementation of ``Solarium\Core\Client\Adapter\AdapterInterface``

``dispatcher_service``
----------------------
service id of your implementation of ``Psr\EventDispatcher\EventDispatcherInterface``

defaults to ``event_dispatcher``

``adapter_class`` vs. ``adapter_service``
-----------------------------------------
only one of these can be configured;

``adapter_service`` takes precedence over ``adapter_class``
