var app = angular.module('adminApp');
app.controller('AccountController', [
    '$cookies', '$routeParams', '$location', 'AuthService', 'ReferenceResource', 'AccountResource'
    , function($cookies, $routeParams, $location, AuthService, ReferenceResource, AccountResource)
{
    var self = this;
    self.accounts = [];
    self.account = null;
    self.queryCriteria = null;
    self.queryResult = null;

    self.temp = {
        email: null,
        dob: {
            month: null,
            day: null,
            year: null
        }
    };

    self.references = {};
    loadReferences();

    function loadReferences()
    {
        self.references.days = new Array(31);
        self.references.months = ReferenceResource.lookup('months');
        self.references.genders = ReferenceResource.lookup('genders');;
    }

    if ($routeParams.accountId && $routeParams.accountId != 'new') {
    	self.account = AccountResource.get({id: $routeParams.accountId}, function(data) {
            // nothing to do, data is updated when async is returned.
        }, function(error) {
            alert(JSON.stringify(error));
        });
    } else {
	    // initialize
	    query();
	}

    this.go = function(path) {
        return $location.path( path );
    };

    this.goToPage = function(pageIdx) {
        var retval = '/?_page=' + pageIdx;
        if (self.queryResult.limit) {
            retval += '&limit=' + self.queryResult.limit;
        }
        $location.path('/').search('_page', pageIdx).search('_limit', self.queryResult.limit);
    };

    /**
     * Is any user selected?
     */
    this.selectedAccount = function() {
        return self.account;
    };

    /**
     * Removes an account
     */
    this.remove = function(account) {
        AccountResource.remove({id:account.uuid}, function(data) {
            // nothing to do, data is updated when async is returned.
            // temp:
            alert('Account: ' + account.displayName + ' was removed. Please refresh page');
        }, function(error) {
            alert(JSON.stringify(error));
        });
    };

    this.doQuery = function(id) {
        query();
    };

    this.getAccount = function(id) {
    	self.account = AccountResource.get(id);
    	if (!self.account) {
    		alert ('Not found for ' + id);
    	}
    };

    /**
     * Submit for update
     */
    this.submit = function() {
        //self.account.primaryEmail = [self.temp.email];
        var dob = new Date(self.temp.dob.year, self.temp.dob.month, self.temp.dob.day);
        self.account.profile.dob = moment(dob).format();
        if (self.account.uuid) {
            // Update existing
            delete self.account._id;
            AccountResource.update({id: self.account.uuid}, self.account);
        } else {
            // Create new
            var newAccount = new AccountResource(self.account);
            newAccount.kind = 'normal';
            newAccount.auth = {
                authSource: 'local',
                username: 'test',
                security: { password: 'test' }
            };
            newAccount.$save();
        }
    };



    function query(criteria) {
        var qparms = $location.search();
        var queryArgs = {
            _meta: 'true',
            _page: qparms._page,
            _limit: qparms._limit
        };

        // initialize
        self.accounts = AccountResource.query2(queryArgs, function(data) {
            self.queryResult = data;
            self.queryResult.numPages = Math.ceil(self.queryResult.totalHits / self.queryResult.limit);
            self.accounts = data.documents;
        }, function(error) {
            alert(JSON.stringify(error));
        });
    };

}]);
