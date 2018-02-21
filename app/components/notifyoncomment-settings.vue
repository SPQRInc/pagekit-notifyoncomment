<template>
    <ul class="uk-tab" v-el:tab>
        <li><a>{{ 'Settings' | trans }}</a></li>
        <li><a>{{ 'Info' | trans }}</a></li>
    </ul>
    <div class="uk-switcher uk-margin" v-el:content>
        <div class="uk-form uk-form-horizontal">
            <h1>{{ 'Notify On Comment Settings' | trans }}</h1>
            <form class="uk-form uk-form-stacked" v-validator="formUser" @submit.prevent="add | valid">
                <h2>{{ 'Notify Users' | trans }}</h2>
                <div class="uk-form-row">
                    <div class="uk-grid" data-uk-margin>
                        <div class="uk-width-large-1-2">
                            <div class="uk-form-row">
                                <select id="form-user" class="uk-width-1-1" v-show="users.length" v-model="newUser" v-validate:required>
                                    <option value="">{{ '- Select User -' | trans }}</option>
                                    <option v-for="user in users" :value="user" :disabled="userMatch(user.id)">{{ user.username }}</option>
                                </select>
                                <div v-else>
                                    {{ 'Loading users...' | trans }}
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-large-1-2">
                            <div class="uk-form-controls">
                                <span class="uk-align-right">
                                    <button class="uk-button" @click.prevent="add | valid">
                                        {{ 'Add' | trans }}
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <hr/>
            <div class="uk-alert"
                 v-if="!package.config.users.length">{{ 'You can add your first user using the input field above. Go ahead!' | trans }}
            </div>
            <ul class="uk-list uk-list-line" v-if="package.config.users.length">
                <li v-for="user in package.config.users">
                    {{ user.username}} <em>({{ user.email }})</em>
                    <span class="uk-align-right">
                        <button class="uk-button uk-button-danger" @click="remove(user)">
                            <i class="uk-icon-remove"></i>
                        </button>
                    </span>
                </li>
            </ul>
            <div class="uk-form-row uk-margin-top">
                <div class="uk-form-controls">
                    <button class="uk-button uk-button-primary" @click="save">{{ 'Save' | trans }}</button>
                </div>
            </div>
        </div>
        <div class="uk-form uk-form-horizontal">
            <h1>{{ 'Notify On Comment Info' | trans }}</h1>
            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Getting help' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <div class="uk-panel uk-panel-box">
                        <p>{{ 'You have problems using this extension? Join the Pagekit community forum.' | trans }}</p>
                        <a class="uk-button uk-width-1-1 uk-button-large" href="https://pagekit-forum.org"
                           target="_blank">Pagekit Forum</a>
                    </div>
                </div>
            </div>
            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Donate' | trans }}</label>
                <div class="uk-form-controls uk-form-controls-text">
                    <div class="uk-panel uk-panel-box">
                        <p>{{ 'Do you like my extensions? They are free. Of course I would like to get a donation, so if you want to, please open the donate link. You may find three possibilities to donate: PayPal, Patreon and Coinhive.' | trans }} </p>
                        <a class="uk-button uk-button-large uk-width-1-1 uk-button-primary"
                           href="https://spqr.wtf/support-me" target="_blank">Donate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

module.exports = {

	settings: true,

	props: ['package'],

	data: function() {
		return {
			users: [],
            newUser: ''
		};
	},

	ready: function () {
		this.tab = UIkit.tab (this.$els.tab, {connect: this.$els.content});
	},

	created: function () {
		this.load();
	},

	methods: {
		load: function load() {
			this.User = this.$resource('api/user{/id}');
			this.User.query({ filter: { status: 1 } }).then(function (res) {
				var data = res.data;
				this.$set('users', data.users);
			}, function () {
				this.$notify('Loading failed.', 'danger');
			});
		},
		save: function save () {
			this.$http.post ('admin/system/settings/config', {
				name: 'spqr/notifyoncomment',
				config: this.package.config
			}).then (function () {
				this.$notify ('Settings saved.', '');
			}).catch (function (response) {
				this.$notify (response.message, 'danger');
			}).finally (function () {
				this.$parent.close ();
			});
		},
		add: function add (e) {
			e.preventDefault ();
			if (!this.newUser) return;

			this.package.config.users.push (this.newUser);
			this.newUser = '';
		},
		remove: function (user) {
			this.package.config.users.$remove (user);
		},
		userMatch: function userMatch(userid) {
			return this.package.config.users.filter(function (usr) {
				return usr.id == userid;
			}).length > 0;
		}
	}
};

window.Extensions.components['notifyoncomment-settings'] = module.exports;
</script>