# Changes in this branch

* Include missing Swagger files to complete the documentation.
* Different schemas for creating and adding a Cart.
* Different JSON for Requests and Responses (to support the client minimum info is required in Requests whereas in Response all needed info is sent).
* Add total Cart price in Response.
* Adapt Swagger doc for last changes.
* Include the error message in Response (apart from the error code which was already being sent).
* Refactor Use Case services so no additional steps need to be taken care from Controller when using them (like getting the Cart that was just deleted or json_decode a string).
* Functional tests have been added to test the whole Request to Response flow (not fully covered).


