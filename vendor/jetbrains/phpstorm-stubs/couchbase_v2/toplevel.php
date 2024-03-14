<?php

namespace DEPTRAC_202403;

/**
 * Couchbase extension stubs
 * Gathered from https://docs.couchbase.com/sdk-api/couchbase-php-client-2.3.0/index.html
 * Maintainer: sergey@couchbase.com
 *
 * https://github.com/couchbase/php-couchbase/tree/master/api
 */
\class_alias("Couchbase\\Cluster", "DEPTRAC_202403\\CouchbaseCluster");
\class_alias("Couchbase\\Bucket", "DEPTRAC_202403\\CouchbaseBucket");
\class_alias("Couchbase\\MutationToken", "DEPTRAC_202403\\CouchbaseMutationToken");
\class_alias("Couchbase\\MutationState", "DEPTRAC_202403\\CouchbaseMutationState");
\class_alias("Couchbase\\BucketManager", "DEPTRAC_202403\\CouchbaseBucketManager");
\class_alias("DEPTRAC_202403\\Couchbase\\ClusterManager", "DEPTRAC_202403\\CouchbaseClusterManager");
\class_alias("DEPTRAC_202403\\Couchbase\\LookupInBuilder", "DEPTRAC_202403\\CouchbaseLookupInBuilder");
\class_alias("DEPTRAC_202403\\Couchbase\\MutateInBuilder", "DEPTRAC_202403\\CouchbaseMutateInBuilder");
\class_alias("DEPTRAC_202403\\Couchbase\\N1qlQuery", "DEPTRAC_202403\\CouchbaseN1qlQuery");
\class_alias("Couchbase\\SearchQuery", "DEPTRAC_202403\\CouchbaseSearchQuery");
\class_alias("DEPTRAC_202403\\Couchbase\\SearchQueryPart", "DEPTRAC_202403\\CouchbaseAbstractSearchQuery");
\class_alias("Couchbase\\QueryStringSearchQuery", "DEPTRAC_202403\\CouchbaseStringSearchQuery");
\class_alias("Couchbase\\MatchSearchQuery", "DEPTRAC_202403\\CouchbaseMatchSearchQuery");
\class_alias("Couchbase\\MatchPhraseSearchQuery", "DEPTRAC_202403\\CouchbaseMatchPhraseSearchQuery");
\class_alias("Couchbase\\PrefixSearchQuery", "DEPTRAC_202403\\CouchbasePrefixSearchQuery");
\class_alias("Couchbase\\RegexpSearchQuery", "DEPTRAC_202403\\CouchbaseRegexpSearchQuery");
\class_alias("Couchbase\\NumericRangeSearchQuery", "DEPTRAC_202403\\CouchbaseNumericRangeSearchQuery");
\class_alias("Couchbase\\DisjunctionSearchQuery", "DEPTRAC_202403\\CouchbaseDisjunctionSearchQuery");
\class_alias("Couchbase\\DateRangeSearchQuery", "DEPTRAC_202403\\CouchbaseDateRangeSearchQuery");
\class_alias("Couchbase\\ConjunctionSearchQuery", "DEPTRAC_202403\\CouchbaseConjunctionSearchQuery");
\class_alias("Couchbase\\BooleanSearchQuery", "DEPTRAC_202403\\CouchbaseBooleanSearchQuery");
\class_alias("Couchbase\\WildcardSearchQuery", "DEPTRAC_202403\\CouchbaseWildcardSearchQuery");
\class_alias("Couchbase\\DocIdSearchQuery", "DEPTRAC_202403\\CouchbaseDocIdSearchQuery");
\class_alias("Couchbase\\BooleanFieldSearchQuery", "DEPTRAC_202403\\CouchbaseBooleanFieldSearchQuery");
\class_alias("Couchbase\\TermSearchQuery", "DEPTRAC_202403\\CouchbaseTermSearchQuery");
\class_alias("Couchbase\\PhraseSearchQuery", "DEPTRAC_202403\\CouchbasePhraseSearchQuery");
\class_alias("Couchbase\\MatchAllSearchQuery", "DEPTRAC_202403\\CouchbaseMatchAllSearchQuery");
\class_alias("Couchbase\\MatchNoneSearchQuery", "DEPTRAC_202403\\CouchbaseMatchNoneSearchQuery");
\class_alias("Couchbase\\DateRangeSearchFacet", "DEPTRAC_202403\\CouchbaseDateRangeSearchFacet");
\class_alias("Couchbase\\NumericRangeSearchFacet", "DEPTRAC_202403\\CouchbaseNumericRangeSearchFacet");
\class_alias("Couchbase\\TermSearchFacet", "DEPTRAC_202403\\CouchbaseTermSearchFacet");
\class_alias("Couchbase\\SearchFacet", "DEPTRAC_202403\\CouchbaseSearchFacet");
\class_alias("DEPTRAC_202403\\Couchbase\\ViewQuery", "DEPTRAC_202403\\CouchbaseViewQuery");
\class_alias("DEPTRAC_202403\\Couchbase\\DocumentFragment", "DEPTRAC_202403\\CouchbaseDocumentFragment");
\class_alias("DEPTRAC_202403\\Couchbase\\Document", "DEPTRAC_202403\\CouchbaseMetaDoc");
\class_alias("DEPTRAC_202403\\Couchbase\\Exception", "DEPTRAC_202403\\CouchbaseException");
\class_alias("DEPTRAC_202403\\Couchbase\\ClassicAuthenticator", "DEPTRAC_202403\\CouchbaseAuthenticator");
\define("DEPTRAC_202403\\COUCHBASE_PERSISTTO_MASTER", 1);
\define("DEPTRAC_202403\\COUCHBASE_PERSISTTO_ONE", 1);
\define("DEPTRAC_202403\\COUCHBASE_PERSISTTO_TWO", 2);
\define("DEPTRAC_202403\\COUCHBASE_PERSISTTO_THREE", 4);
\define("DEPTRAC_202403\\COUCHBASE_REPLICATETO_ONE", 16);
\define("DEPTRAC_202403\\COUCHBASE_REPLICATETO_TWO", 32);
\define("DEPTRAC_202403\\COUCHBASE_REPLICATETO_THREE", 64);
\define("DEPTRAC_202403\\COUCHBASE_SUCCESS", 0);
\define("DEPTRAC_202403\\COUCHBASE_AUTH_CONTINUE", 1);
\define("DEPTRAC_202403\\COUCHBASE_AUTH_ERROR", 2);
\define("DEPTRAC_202403\\COUCHBASE_DELTA_BADVAL", 3);
\define("DEPTRAC_202403\\COUCHBASE_E2BIG", 4);
\define("DEPTRAC_202403\\COUCHBASE_EBUSY", 5);
\define("DEPTRAC_202403\\COUCHBASE_EINTERNAL", 6);
\define("DEPTRAC_202403\\COUCHBASE_EINVAL", 7);
\define("DEPTRAC_202403\\COUCHBASE_ENOMEM", 8);
\define("DEPTRAC_202403\\COUCHBASE_ERANGE", 9);
\define("DEPTRAC_202403\\COUCHBASE_ERROR", 10);
\define("DEPTRAC_202403\\COUCHBASE_ETMPFAIL", 11);
\define("DEPTRAC_202403\\COUCHBASE_KEY_EEXISTS", 12);
\define("DEPTRAC_202403\\COUCHBASE_KEY_ENOENT", 13);
\define("DEPTRAC_202403\\COUCHBASE_DLOPEN_FAILED", 14);
\define("DEPTRAC_202403\\COUCHBASE_DLSYM_FAILED", 15);
\define("DEPTRAC_202403\\COUCHBASE_NETWORK_ERROR", 16);
\define("DEPTRAC_202403\\COUCHBASE_NOT_MY_VBUCKET", 17);
\define("DEPTRAC_202403\\COUCHBASE_NOT_STORED", 18);
\define("DEPTRAC_202403\\COUCHBASE_NOT_SUPPORTED", 19);
\define("DEPTRAC_202403\\COUCHBASE_UNKNOWN_COMMAND", 20);
\define("DEPTRAC_202403\\COUCHBASE_UNKNOWN_HOST", 21);
\define("DEPTRAC_202403\\COUCHBASE_PROTOCOL_ERROR", 22);
\define("DEPTRAC_202403\\COUCHBASE_ETIMEDOUT", 23);
\define("DEPTRAC_202403\\COUCHBASE_CONNECT_ERROR", 24);
\define("DEPTRAC_202403\\COUCHBASE_BUCKET_ENOENT", 25);
\define("DEPTRAC_202403\\COUCHBASE_CLIENT_ENOMEM", 26);
\define("DEPTRAC_202403\\COUCHBASE_CLIENT_ENOCONF", 27);
\define("DEPTRAC_202403\\COUCHBASE_EBADHANDLE", 28);
\define("DEPTRAC_202403\\COUCHBASE_SERVER_BUG", 29);
\define("DEPTRAC_202403\\COUCHBASE_PLUGIN_VERSION_MISMATCH", 30);
\define("DEPTRAC_202403\\COUCHBASE_INVALID_HOST_FORMAT", 31);
\define("DEPTRAC_202403\\COUCHBASE_INVALID_CHAR", 32);
\define("DEPTRAC_202403\\COUCHBASE_DURABILITY_ETOOMANY", 33);
\define("DEPTRAC_202403\\COUCHBASE_DUPLICATE_COMMANDS", 34);
\define("DEPTRAC_202403\\COUCHBASE_NO_MATCHING_SERVER", 35);
\define("DEPTRAC_202403\\COUCHBASE_BAD_ENVIRONMENT", 36);
\define("DEPTRAC_202403\\COUCHBASE_BUSY", 37);
\define("DEPTRAC_202403\\COUCHBASE_INVALID_USERNAME", 38);
\define("DEPTRAC_202403\\COUCHBASE_CONFIG_CACHE_INVALID", 39);
\define("DEPTRAC_202403\\COUCHBASE_SASLMECH_UNAVAILABLE", 40);
\define("DEPTRAC_202403\\COUCHBASE_TOO_MANY_REDIRECTS", 41);
\define("DEPTRAC_202403\\COUCHBASE_MAP_CHANGED", 42);
\define("DEPTRAC_202403\\COUCHBASE_INCOMPLETE_PACKET", 43);
\define("DEPTRAC_202403\\COUCHBASE_ECONNREFUSED", 44);
\define("DEPTRAC_202403\\COUCHBASE_ESOCKSHUTDOWN", 45);
\define("DEPTRAC_202403\\COUCHBASE_ECONNRESET", 46);
\define("DEPTRAC_202403\\COUCHBASE_ECANTGETPORT", 47);
\define("DEPTRAC_202403\\COUCHBASE_EFDLIMITREACHED", 48);
\define("DEPTRAC_202403\\COUCHBASE_ENETUNREACH", 49);
\define("DEPTRAC_202403\\COUCHBASE_ECTL_UNKNOWN", 50);
\define("DEPTRAC_202403\\COUCHBASE_ECTL_UNSUPPMODE", 51);
\define("DEPTRAC_202403\\COUCHBASE_ECTL_BADARG", 52);
\define("DEPTRAC_202403\\COUCHBASE_EMPTY_KEY", 53);
\define("DEPTRAC_202403\\COUCHBASE_SSL_ERROR", 54);
\define("DEPTRAC_202403\\COUCHBASE_SSL_CANTVERIFY", 55);
\define("DEPTRAC_202403\\COUCHBASE_SCHEDFAIL_INTERNAL", 56);
\define("DEPTRAC_202403\\COUCHBASE_CLIENT_FEATURE_UNAVAILABLE", 57);
\define("DEPTRAC_202403\\COUCHBASE_OPTIONS_CONFLICT", 58);
\define("DEPTRAC_202403\\COUCHBASE_HTTP_ERROR", 59);
\define("DEPTRAC_202403\\COUCHBASE_DURABILITY_NO_MUTATION_TOKENS", 60);
\define("DEPTRAC_202403\\COUCHBASE_UNKNOWN_MEMCACHED_ERROR", 61);
\define("DEPTRAC_202403\\COUCHBASE_MUTATION_LOST", 62);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_PATH_ENOENT", 63);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_PATH_MISMATCH", 64);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_PATH_EINVAL", 65);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_PATH_E2BIG", 66);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_DOC_E2DEEP", 67);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_VALUE_CANTINSERT", 68);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_DOC_NOTJSON", 69);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_NUM_ERANGE", 70);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_BAD_DELTA", 71);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_PATH_EEXISTS", 72);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_MULTI_FAILURE", 73);
\define("DEPTRAC_202403\\COUCHBASE_SUBDOC_VALUE_E2DEEP", 74);
\define("DEPTRAC_202403\\COUCHBASE_EINVAL_MCD", 75);
\define("DEPTRAC_202403\\COUCHBASE_EMPTY_PATH", 76);
\define("DEPTRAC_202403\\COUCHBASE_UNKNOWN_SDCMD", 77);
\define("DEPTRAC_202403\\COUCHBASE_ENO_COMMANDS", 78);
\define("DEPTRAC_202403\\COUCHBASE_QUERY_ERROR", 79);
\define("DEPTRAC_202403\\COUCHBASE_TMPFAIL", 11);
\define("DEPTRAC_202403\\COUCHBASE_KEYALREADYEXISTS", 12);
\define("DEPTRAC_202403\\COUCHBASE_KEYNOTFOUND", 13);
