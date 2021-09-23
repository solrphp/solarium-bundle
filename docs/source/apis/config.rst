Config API
==========
| see the `official Config API reference <https://solr.apache.org/guide/config-api.html>`_ for the api's official documentation.
| currently supported features and commands:

console commands
----------------
| in order to use these commands, make sure you've got at least one :doc:`endpoint <../reference/configuration/endpoints>`, :doc:`client <../reference/configuration/endpoints>` and :doc:`config <../reference/configuration/configs>` configured.

update
~~~~~~
the config update is available by running

.. code-block:: bash

    $ php bin/console solr:config:update <core-name>

this command compares the properties configured under ``solr_configs`` for given core name and syncs the ``configoverlay.json`` accordingly.

