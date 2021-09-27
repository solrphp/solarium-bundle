Config API
==========
| see the `official Config API reference <https://solr.apache.org/guide/config-api.html>`_ for the api's official documentation.

.. |api-manager| replace:: ``ConfigManager``

.. include:: ../partials/api-manager.rstinc

.. include:: ../partials/config-handler.rstinc

.. include:: ../partials/console-commands.rstinc

update
~~~~~~
the config update is available by running

.. code-block:: bash

    $ php bin/console solr:config:update <core-name>

this command compares the properties configured under ``solr_configs`` for given core name and syncs the ``configoverlay.json`` accordingly.

