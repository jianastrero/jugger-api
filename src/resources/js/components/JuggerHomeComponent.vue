<template>
    <div class="jugger-full-screen jugger-p-0 bg-primary">
        <div class="position-fixed" style="top: 2em; right: 2em; z-index: 5000;">
            <div v-for="message in messages" :class="{'alert-success': message.success, 'alert-danger': !message.success}" class="alert alert-dismissible fade show" role="alert">
                {{ message.message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="jugger-match-parent d-flex flex-row shadow position-relative jugger-overflow-hidden">
            <div v-on:mouseover="showNavOnOver" v-on:mouseout="showNavOnHoverOut" class="d-flex flex-column align-content-start justify-content-center jugger-red-dark-bg jugger-nav-icon-width jugger-overflow-hidden jugger-position-relative jugger-z-6">
                <a :class="{'pulse': navOver}" class="flex-grow-0 animated jugger-nav-icon-width jugger-nav-icon-height" href="https://github.com/jianastrero/JuggerAPI">
                    <img :src="rootUrl + '/jianastrero/jugger-api/images/jugger-api-logo-large.png'" class="jugger-match-parent"/>
                </a>
                <a :href="rootUrl + '/jugger-api/home'" class="flex-grow-0 bg-primary text-light text-center align-middle jugger-nav-icon-width jugger-nav-icon-height">
                    <i class="fas fa-code fa-lg"></i>
                </a>
                <div class="flex-grow-1"></div>
                <a :href="rootUrl + '/jugger-api/logout'" class="flex-grow-0 text-light text-center align-middle jugger-nav-icon-width jugger-nav-icon-height jugger-red-dark-bg-nav">
                    <i class="fas fa-sign-out-alt fa-lg"></i>
                </a>
            </div>
            <div :class="{'jugger-nav-width-in-anim': navOver}" class="flex-grow-0 d-flex flex-column jugger-nav-text-width position-absolute jugger-match-parent-height jugger-nav-text-left jugger-z-5 jugger-red-dark-bg-nav-text jugger-pointer-none jugger-animated jugger-nav-width-out-anim jugger-overflow-hidden text-truncate">
                <div class="flex-grow-0 jugger-nav-icon-height"></div>
                <div class="flex-grow-0 text-light jugger-inline-block jugger-nav-icon-height jugger-nav-text-padding jugger-font-oswald jugger-red-dark-bg-nav-active">
                    JUGGER API'S
                </div>
                <div class="flex-grow-1 jugger-nav-icon-height"></div>
                <div class="flex-grow-0 text-light jugger-inline-block jugger-nav-icon-height jugger-nav-text-padding jugger-font-oswald">
                    LOGOUT
                </div>
            </div>
            <div class="flex-grow-1 d-flex flex-column bg-light p-3">
                <div class="flex-grow-0 d-flex flex-row mb-3">
                    <div class="flex-grow-0 breadcrumb-parent">
                        <nav aria-label="breadcrumb" style="display: inline-block;">
                            <ol class="breadcrumb jugger-red-dark-bg m-0">
                                <li class="breadcrumb-item text-light active" aria-current="page">Jugger API's</li>
                            </ol>
                        </nav>
                        <i v-if="isLoading" class="fas fa-cog fa-lg fa-spin text-primary ml-2"></i>
                    </div>
                    <div class="flex-grow-1"></div>
                    <div class="flex-grow-0 d-flex align-items-end">
                        <form v-on:submit.prevent="doSearch" class="position-relative"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Press enter when you are done entering your search term">
                            <input v-model="searchTerm" type="text" class="form-control jugger-icon-right-input" placeholder="Search">
                            <i class="fas fa-search jugger-icon-right text-primary"></i>
                        </form>
                    </div>
                </div>
                <div class="flex-grow-1 jugger-table-round jugger-overflow-hidden position-relative mb-3 jugger-list-bg" style="overflow: auto;">
                    <div style="position: absolute;">
                        <table class="table table-borderless text-dark m-0 jugger-table-round position-sticky sticky-top jugger-z-4" style="table-layout: fixed;">
                            <thead class="bg-primary text-light">
                            <tr>
                                <th scope="col" style="width: 15%;">Model Name</th>
                                <th scope="col">Slug</th>
                                <th scope="col" style="width: 15%;">Columns</th>
                                <th scope="col" style="width: 15%;">Sort</th>
                                <th scope="col" style="width: 5%;">List</th>
                                <th scope="col" style="width: 5%;">Read</th>
                                <th scope="col" style="width: 5%;">Create</th>
                                <th scope="col" style="width: 5%;">Update</th>
                                <th scope="col" style="width: 5%;">Delete</th>
                                <th scope="col">Paginate Item Count</th>
                                <th scope="col" style="width: 10%;">Action</th>
                            </tr>
                            </thead>
                        </table>
                        <div class="table-responsive">
                            <table class="table table-bordered text-dark m-0 jugger-list-bg" style="table-layout: fixed;">
                                <tbody>
                                <tr v-for="(item, index) in page.data" :key="item.id">
                                    <td style="width: 15%;">
                                        <div>{{ item.model_name.split('\\')[1] }}</div>
                                    </td>
                                    <td nowrap>
                                        <div>{{ item.slug }}</div>
                                    </td>
                                    <td class="p-0" style="width: 15%;">
                                        <div :class="{'bg-success': item.column_override, 'bg-warning': !item.column_override}" class="p-2 text-light text-center">CAN{{ item.column_override ? "" : "\'T" }} OVERRIDE</div>
                                        <ul class="m-2">
                                            <li v-for="(column, index) in item.columns" :key="index" class="text-capitalize">{{ column.replace(/_/g, ' ') }}</li>
                                        </ul>
                                    </td>
                                    <td class="p-0" style="width: 15%;">
                                        <div :class="{'bg-success': item.sort_override, 'bg-warning': !item.sort_override}" class="p-2 text-light text-center">CAN{{ item.sort_override ? "" : "\'T" }} OVERRIDE</div>
                                        <ul class="m-2">
                                            <li v-for="(sort, index) in item.sort" :key="index" class="text-capitalize">{{ sort.replace(/_/g, ' ') }}</li>
                                        </ul>
                                    </td>
                                    <td style="width: 5%;">
                                        <i :class="{'fa-check-circle text-success': item.list, 'fa-times-circle text-danger': !item.list}" class="fas fa-lg"></i>
                                    </td>
                                    <td style="width: 5%;">
                                        <i :class="{'fa-check-circle text-success': item.read, 'fa-times-circle text-danger': !item.read}" class="fas fa-lg"></i>
                                    </td>
                                    <td style="width: 5%;">
                                        <i :class="{'fa-check-circle text-success': item.create, 'fa-times-circle text-danger': !item.create}" class="fas fa-lg"></i>
                                    </td>
                                    <td style="width: 5%;">
                                        <i :class="{'fa-check-circle text-success': item.update, 'fa-times-circle text-danger': !item.update}" class="fas fa-lg"></i>
                                    </td>
                                    <td style="width: 5%;">
                                        <i :class="{'fa-check-circle text-success': item.delete, 'fa-times-circle text-danger': !item.delete}" class="fas fa-lg"></i>
                                    </td>
                                    <td>
                                        <div>
                                            {{ item.paginate_item_count }}
                                        </div>
                                    </td>
                                    <td class="m-0 p-0" style="width: 10%;">
                                        <button v-on:click="editModalOpen(item.id)" class="btn btn-warning p-2 text-center text-light" style="width: 100%; display: block; border-radius: 0;">EDIT</button>
                                        <button v-on:click="deleteModalOpen(item.id)" class="btn btn-danger p-2 text-center text-light" style="width: 100%; display: block; border-radius: 0;">DELETE</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-0 d-flex flex-row">
                    <div class="flex-grow-0">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li :class="{'disabled': page.prev_page_url == null, 'pointer-events-none': page.prev_page_url == null}" class="page-item" v-on:click="fetchList(page.current_page-1, page.prev_page_url != null)">
                                    <a :class="{'text-primary': page.prev_page_url != null}" class="page-link">
                                        <span aria-hidden="true">&laquo;</span>
                                        <span class="sr-only">Previous</span>
                                    </a>
                                </li>
                                <li v-for="(n, index) in pages" :key="index" :class="{'active jugger-pointer-none': index + 1 === page.current_page}" v-on:click="fetchList(index + 1)" class="page-item"><a class="page-link cursor-pointer">{{ index + 1 }}</a></li>
                                <li :class="{'disabled': page.last_page == page.current_page, 'pointer-events-none': page.last_page == page.current_page}" class="page-item" v-on:click="fetchList(page.current_page+1, page.last_page != page.current_page)">
                                    <a :class="{'text-primary': page.last_page != page.current_page}" class="page-link">
                                        <span aria-hidden="true">&raquo;</span>
                                        <span class="sr-only">Next</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="flex-grow-0">
                        <div class="form-inline">
                            <label for="selectVersion" class="pl-3 pr-2">Version </label>
                            <select v-model="selectedVersion" id="selectVersion" class="form-control">
                                <option v-for="(version, index) in versions" :key="index" :value="version.version">
                                    v{{ version.version }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="flex-grow-1"></div>
                    <div class="flex-grow-0">
                        <loading-button-component
                                v-on:click.native="addNewModalOpen"
                                class="jugger-font-oswald"
                                :button="'btn-success'"
                                :buttonType="'button'"
                                :buttonText="'NEW'"
                                :showTextWhileLoading="false"
                                :icon="'fa-plus'"
                                :iconLoading="'fa-sync'"
                                :loading="isLoading">
                        </loading-button-component>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <form v-on:submit.prevent="addNew" class="modal-content jugger-table-round">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Add a New API</h5>
                        <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputModel">Model <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Choose a model that is ready for JuggerAPI<br/><small><strong>NOTE: make sure your models uses the traits CanMutate and HasTable</strong></small>"></i></label>
                                    <select v-model="addInput.model" id="inputModel" class="form-control">
                                        <option selected disabled>Choose a Model...</option>
                                        <option v-for="(model, index) in Object.keys(models)" :key="index">
                                            {{ model }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Slug <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="A Slug is the last part of the url where the API will be located. It acts like:<br/><small><strong>http://mydomain.com/api/slug</strong></small><br/><strong>It is recommended to use plural words but its not necessary for <span class='text-primary'>JuggerAPI</span> to work. <span class='text-primary'>JuggerAPI</span> is good enough to make the words plural or singular when you access them using the url</strong>"></i></label>
                                    <input v-model="addInput.slug" type="text" class="form-control" id="inputSlug" aria-describedby="slugHelp" placeholder="Enter slug">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Columns <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Columns that will be retrieved by default on <strong>list or read API's</strong>"></i></label>
                                    <div v-for="(column, index) in models[addInput.model]" :key="index" class="form-check ml-4 mr-4">
                                        <input  v-model="addInput.columns" class="form-check-input" type="checkbox" :value="column" :id="column + 'ColumnInput'">
                                        <label class="form-check-label" :for="column + 'ColumnInput'">
                                            {{ column }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Sort <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Default sorting order on <strong>list API</strong>"></i></label>
                                    <div v-for="(column, index) in models[addInput.model]" :key="index" class="form-check ml-4 mr-4">
                                        <input  v-model="addInput.sort" class="form-check-input" type="radio" :value="column+',asc'" :id="column + 'SortInput'">
                                        <label class="form-check-label" :for="column + 'SortInput'">
                                            {{ column }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Allow Columns Override <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows the request to override how retrieved data will look by selecting columns"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.columnOverride" class="form-check-input" type="checkbox" value="true" id="columnOverrideInput">
                                        <label class="form-check-label" for="columnOverrideInput">
                                            Allow column override
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Allow Sort Override <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows the request to override how retrieved data will look by sorting"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.sortOverride" class="form-check-input" type="checkbox" value="true" id="sortOverrideInput">
                                        <label class="form-check-label" for="sortOverrideInput">
                                            Allow sort override
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Create <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to create a record using the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.createAllowed" class="form-check-input" type="checkbox" value="true" id="createAllowedInput">
                                        <label class="form-check-label" for="createAllowedInput">
                                            Allow Create
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Update <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to update a record using the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.updateAllowed" class="form-check-input" type="checkbox" value="true" id="updateAllowedInput">
                                        <label class="form-check-label" for="updateAllowedInput">
                                            Allow Update
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Delete <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to delete a record using the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.deleteAllowed" class="form-check-input" type="checkbox" value="true" id="deleteAllowedInput">
                                        <label class="form-check-label" for="deleteAllowedInput">
                                            Allow Delete
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">List <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to retrieve a list from the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.listAllowed" class="form-check-input" type="checkbox" value="true" id="listAllowedInput">
                                        <label class="form-check-label" for="listAllowedInput">
                                            Allow List
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Read <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to retrieve a single item from the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="addInput.readAllowed" class="form-check-input" type="checkbox" value="true" id="readAllowedInput">
                                        <label class="form-check-label" for="readAllowedInput">
                                            Allow Read
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputPaginationItemCount">Column Item Count <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="The number of items on each page when fetching a list"></i></label>
                                    <input v-model="addInput.paginationItemCount" type="number" class="form-control" id="inputPaginationItemCount" aria-describedby="slugHelp" placeholder="Enter Pagination Count">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="!isLoading" v-on:click="clearAddInput" type="button" class="btn btn-danger jugger-font-oswald" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;</button>
                        <loading-button-component
                                class="jugger-font-oswald"
                                :button="'btn-success'"
                                :buttonType="'submit'"
                                :buttonText="'ADD'"
                                :showTextWhileLoading="false"
                                :icon="'fa-plus'"
                                :iconLoading="'fa-sync'"
                                :loading="isLoading">
                        </loading-button-component>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <form v-on:submit.prevent="editSelected" class="modal-content jugger-table-round">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editExampleModalCenterTitle">Edit API</h5>
                        <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputModelEdit">Model <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Choose a model that is ready for JuggerAPI<br/><small><strong>NOTE: make sure your models uses the traits CanMutate and HasTable</strong></small>"></i></label>
                                    <select v-model="editInput.model" id="inputModelEdit" class="form-control">
                                        <option selected disabled>Choose a Model...</option>
                                        <option v-for="(model, index) in Object.keys(models)" :key="index" :value="model">
                                            {{ model }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlugEdit">Slug <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="A Slug is the last part of the url where the API will be located. It acts like:<br/><small><strong>http://mydomain.com/api/slug</strong></small><br/><strong>It is recommended to use plural words but its not necessary for <span class='text-primary'>JuggerAPI</span> to work. <span class='text-primary'>JuggerAPI</span> is good enough to make the words plural or singular when you access them using the url</strong>"></i></label>
                                    <input v-model="editInput.slug" type="text" class="form-control" id="inputSlugEdit" aria-describedby="slugHelp" placeholder="Enter slug">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlugEdit">Columns <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Columns that will be retrieved by default on <strong>list or read API's</strong>"></i></label>
                                    <div v-for="(column, index) in models[editInput.model]" :key="index" class="form-check ml-4 mr-4">
                                        <input  v-model="editInput.columns" class="form-check-input" type="checkbox" :value="column" :id="column + 'ColumnInputEdit'">
                                        <label class="form-check-label" :for="column + 'ColumnInputEdit'">
                                            {{ column }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlugEdit">Sort <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Default sorting order on <strong>list API</strong>"></i></label>
                                    <div v-for="(column, index) in models[editInput.model]" :key="index" class="form-check ml-4 mr-4">
                                        <input  v-model="editInput.sort" class="form-check-input" type="radio" :value="column+',asc'" :id="column + 'SortInputEdit'">
                                        <label class="form-check-label" :for="column + 'SortInputEdit'">
                                            {{ column }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Allow Columns Override <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows the request to override how retrieved data will look by selecting columns"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.columnOverride" class="form-check-input" type="checkbox" id="columnOverrideInputEdit">
                                        <label class="form-check-label" for="columnOverrideInputEdit">
                                            Allow column override
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <label for="inputSlug">Allow Sort Override <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows the request to override how retrieved data will look by sorting"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.sortOverride" class="form-check-input" type="checkbox" id="sortOverrideInputEdit">
                                        <label class="form-check-label" for="sortOverrideInputEdit">
                                            Allow sort override
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Create <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to create a record using the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.createAllowed" class="form-check-input" type="checkbox" id="createAllowedInputEdit">
                                        <label class="form-check-label" for="createAllowedInputEdit">
                                            Allow Create
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Update <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to update a record using the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.updateAllowed" class="form-check-input" type="checkbox" id="updateAllowedInputEdit">
                                        <label class="form-check-label" for="updateAllowedInputEdit">
                                            Allow Update
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Delete <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to delete a record using the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.deleteAllowed" class="form-check-input" type="checkbox" id="deleteAllowedInputEdit">
                                        <label class="form-check-label" for="deleteAllowedInputEdit">
                                            Allow Delete
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">List <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to retrieve a list from the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.listAllowed" class="form-check-input" type="checkbox" id="listAllowedInputEdit">
                                        <label class="form-check-label" for="listAllowedInputEdit">
                                            Allow List
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputSlug">Read <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="Allows a request to retrieve a single item from the model"></i></label>
                                    <div class="form-check ml-4 mr-4">
                                        <input v-model="editInput.readAllowed" class="form-check-input" type="checkbox" id="readAllowedInputEdit">
                                        <label class="form-check-label" for="readAllowedInputEdit">
                                            Allow Read
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col col-md-4">
                                <div class="form-group">
                                    <label for="inputPaginationItemCountEdit">Column Item Count <i class="fas fa-question-circle"  data-toggle="tooltip" data-placement="bottom" data-html="true" title="The number of items on each page when fetching a list"></i></label>
                                    <input v-model="editInput.paginationItemCount" type="number" class="form-control" id="inputPaginationItemCountEdit" aria-describedby="slugHelp" placeholder="Enter Pagination Count">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="!isLoading" v-on:click="clearEditInput" type="button" class="btn btn-danger jugger-font-oswald" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;</button>
                        <loading-button-component
                                class="jugger-font-oswald"
                                :button="'btn-success'"
                                :buttonType="'submit'"
                                :buttonText="'SAVE'"
                                :showTextWhileLoading="false"
                                :icon="'fa-check'"
                                :iconLoading="'fa-sync'"
                                :loading="isLoading">
                        </loading-button-component>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModal" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <form v-on:submit.prevent="deleteSelected" class="modal-content jugger-table-round">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalCenterTitle">Delete API</h5>
                        <button v-if="!isLoading" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong class="text-danger">Hold down your horses. Are you sure that you want to delete this API?</strong>
                    </div>
                    <div class="modal-footer">
                        <button v-if="!isLoading" v-on:click="resetSelected" type="button" class="btn btn-warning jugger-font-oswald text-light" data-dismiss="modal"><i class="fas fa-times"></i>&nbsp;&nbsp;&nbsp;CANCEL&nbsp;&nbsp;</button>
                        <loading-button-component
                                class="jugger-font-oswald"
                                :button="'btn-danger'"
                                :buttonType="'submit'"
                                :buttonText="'DELETE'"
                                :showTextWhileLoading="false"
                                :icon="'fa-trash'"
                                :iconLoading="'fa-sync'"
                                :loading="isLoading">
                        </loading-button-component>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import LoadingButtonComponent from "./ui/LoadingButtonComponent.vue";

    export default {
        components: {LoadingButtonComponent},
        name: 'JuggerHomeComponent',
        props: [
            'rootUrl',
            'propModels',
            'propVersions',
        ],
        data() {
            return {
                name: 'JuggerHomeComponent',
                navOver: false,
                isLoading: false,
                models: [],
                versions: [],
                selectedVersion: 1,
                page: [],
                pages: [],
                tempPage: [],
                messages: [],
                selected: -1,
                searchTerm: '',
                addInput: {
                    model: '',
                    slug: '',
                    columns: [],
                    sort: '',
                    columnOverride: false,
                    sortOverride: true,
                    listAllowed: true,
                    readAllowed: true,
                    createAllowed: true,
                    updateAllowed: true,
                    deleteAllowed: true,
                    paginationItemCount: 50
                },
                editInput: {
                    model: '',
                    slug: '',
                    columns: [],
                    sort: '',
                    columnOverride: false,
                    sortOverride: true,
                    listAllowed: true,
                    readAllowed: true,
                    createAllowed: true,
                    updateAllowed: true,
                    deleteAllowed: true,
                    paginationItemCount: 50
                }
            }
        },
        methods: {
            addMessage(success, message) {
                this.messages.push({'success':success, 'message':message});
            },
            genericError(error) {
                console.log('error', error);
                this.addMessage(false, 'Something went wrong, please try again later');
            },
            handleError(errors) {
                if (this.page.length === 0) {
                    this.page = this.tempPage;
                    this.getAvailablePages();
                }
                this.addMessage(false, 'Something went wrong while fetching, please try again');
            },
            showNavOnOver() {
                this.navOver = true;
            },
            showNavOnHoverOut() {
                this.navOver = false;
            },
            getAvailablePages() {
                let len = this.pages.length;
                for (var i = 0; i < len; i++) {
                    this.pages.pop();
                }

                let currentPage = this.page.current_page;
                let lastPage = this.page.last_page;

                let canWrapLeft = currentPage - 1 >= 1;
                let canWrapRight = currentPage + 1 <= lastPage;

                if (!canWrapRight && currentPage - 2 >= 1) {
                    this.pages.push(currentPage - 2);
                }
                if (canWrapLeft) {
                    this.pages.push(currentPage - 1);
                }
                this.pages.push(currentPage);
                if (canWrapRight) {
                    this.pages.push(currentPage + 1);
                }
                if (!canWrapLeft && currentPage + 2 <= lastPage) {
                    this.pages.push(currentPage + 2);
                }
            },
            fetchList(page = 1, clickable = true) {
                if (clickable) {
                    if (!this.isLoading) {
                        this.tempPage = this.page;
                        this.page = [];
                        this.pages = [];
                        this.isLoading = true;
                        var tSearch = "";
                        if (this.searchTerm.trim() !== '') {
                            tSearch = ',' + this.searchTerm;
                        }
                        fetch(this.rootUrl + '/v1/api/jugger-api-routes?page=' + page + '&q=version' + ':' + this.selectedVersion + tSearch, {
                            mode: 'cors',
                            method: 'get',
                            headers: {
                                'Authorization': 'Bearer '  + this.$session.get('accessToken'),
                                "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                                "Accept": "application/json"
                            }
                        })
                            .then(this.json)
                            .then(this.handleJson)
                            .catch(this.genericError);
                    }
                }
            },
            handleJson(json) {
                this.isLoading = false;
                this.handle(json, this.success, this.handleError);
            },
            success(json) {
                this.page = json;
                this.getAvailablePages();
            },
            handleAllErrors(errors) {
                if (errors.code === 404) {
                    this.addMessage(false, 'Item does not exist');
                } else if (errors.code === 405) {
                    this.addMessage(false, 'Apologies but, you are not allowed to do that');
                } else if (errors.code === 422) {
                    this.addMessage(false, 'Some of your input are invalid');
                } else {
                    this.genericError();
                }
            },
            addNewModalOpen() {
                $('#addModal').modal({
                    backdrop: 'static'
                });
            },
            addNew() {
                let body = encodeURI(
                    'model_name=App\\' + this.addInput.model + '&' +
                    'slug=' + this.addInput.slug + '&' +
                    'columns=' + JSON.stringify(this.addInput.columns) + '&' +
                    'sort=' + JSON.stringify(this.addInput.sort.split(',')) + '&' +
                    'column_override=' + this.addInput.columnOverride + '&' +
                    'sort_override=' + this.addInput.sortOverride + '&' +
                    'create=' + this.addInput.createAllowed + '&' +
                    'read=' + this.addInput.readAllowed + '&' +
                    'update=' + this.addInput.updateAllowed + '&' +
                    'delete=' + this.addInput.deleteAllowed + '&' +
                    'list=' + this.addInput.listAllowed + '&' +
                    'paginate_item_count=' + this.addInput.paginationItemCount + '&' +
                    'version=' + this.selectedVersion
                );

                this.isLoading = true;
                fetch(this.rootUrl + '/v1/api/jugger-api-routes', {
                    mode: 'cors',
                    method: 'post',
                    headers: {
                        'Authorization': 'Bearer '  + this.$session.get('accessToken'),
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                        "Accept": "application/json"
                    },
                    body: body
                })
                    .then(this.json)
                    .then(this.handleAdd)
                    .catch(this.genericError);
            },
            handleAdd(json) {
                this.isLoading = false;
                this.handle(json, this.addSuccess, this.handleAllErrors);
            },
            addSuccess(json) {
                $('#addModal').modal('hide');
                this.addMessage(true, 'Successfully Added');
                this.clearAddInput();
                this.fetchList();
            },
            clearAddInput() {
                this.addInput.model = '';
                this.addInput.slug = '';
                this.addInput.columns = [];
                this.addInput.sort = [];
                this.addInput.columnOverride = false;
                this.addInput.sortOverride = true;
                this.addInput.listAllowed = true;
                this.addInput.readAllowed = true;
                this.addInput.createAllowed = true;
                this.addInput.updateAllowed = true;
                this.addInput.deleteAllowed = true;
                this.addInput.paginationItemCount = 50;
            },
            getItem(id) {
                for (var i = 0; i < this.page.data.length; i++) {
                    if (this.page.data[i].id === id)
                        return this.page.data[i];
                }
                return null;
            },
            editModalOpen(id) {
                this.setSelected(id);
                let item = this.getItem(id);
                this.editInput.model = item.model_name.split('\\')[1];
                this.editInput.slug = item.slug;
                this.editInput.columns = item.columns;
                this.editInput.sort = JSON.stringify(item.sort).split('[')[1].split(']')[0].replace(new RegExp('"', 'g'),'');
                this.editInput.columnOverride = item.column_override;
                this.editInput.sortOverride = item.sort_override;
                this.editInput.listAllowed = item.list;
                this.editInput.readAllowed = item.read;
                this.editInput.createAllowed = item.create;
                this.editInput.updateAllowed = item.update;
                this.editInput.deleteAllowed = item.delete;
                this.editInput.paginationItemCount = item.paginate_item_count;
                console.log('page',this.page);
                console.log('item',item);
                console.log('editInput',this.editInput);
                $('#editModal').modal({
                    backdrop: 'static'
                });
            },
            editSelected() {
                let body = encodeURI(
                    'model_name=App\\' + this.editInput.model + '&' +
                    'slug=' + this.editInput.slug + '&' +
                    'columns=' + JSON.stringify(this.editInput.columns) + '&' +
                    'sort=' + JSON.stringify(this.editInput.sort.split(',')) + '&' +
                    'column_override=' + this.editInput.columnOverride + '&' +
                    'sort_override=' + this.editInput.sortOverride + '&' +
                    'create=' + this.editInput.createAllowed + '&' +
                    'read=' + this.editInput.readAllowed + '&' +
                    'update=' + this.editInput.updateAllowed + '&' +
                    'delete=' + this.editInput.deleteAllowed + '&' +
                    'list=' + this.editInput.listAllowed + '&' +
                    'paginate_item_count=' + this.editInput.paginationItemCount + '&' +
                    'version=' + this.selectedVersion
                );

                this.isLoading = true;
                fetch(this.rootUrl + '/v1/api/jugger-api-route/' + this.selected, {
                    mode: 'cors',
                    method: 'put',
                    headers: {
                        'Authorization': 'Bearer '  + this.$session.get('accessToken'),
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                        "Accept": "application/json"
                    },
                    body: body
                })
                    .then(this.json)
                    .then(this.handleEdit)
                    .catch(this.genericError);
            },
            handleEdit(json) {
                this.isLoading = false;
                this.handle(json, this.editSuccess, this.handleAllErrors);
            },
            editSuccess(json) {
                $('#editModal').modal('hide');
                this.addMessage(true, 'Successfully Updated');
                this.clearEditInput();
                this.fetchList();
            },
            clearEditInput() {
                this.editInput.model = '';
                this.editInput.slug = '';
                this.editInput.columns = [];
                this.editInput.sort = [];
                this.editInput.columnOverride = false;
                this.editInput.sortOverride = true;
                this.editInput.listAllowed = true;
                this.editInput.readAllowed = true;
                this.editInput.createAllowed = true;
                this.editInput.updateAllowed = true;
                this.editInput.deleteAllowed = true;
                this.editInput.paginationItemCount = 50;
            },
            deleteModalOpen(id) {
                this.setSelected(id);
                $('#deleteModal').modal({
                    backdrop: 'static'
                });
            },
            resetSelected() {
                this.selected = -1;
            },
            setSelected(id) {
                this.selected = id;
            },
            deleteSelected() {
                this.isLoading = true;
                fetch(this.rootUrl + '/v1/api/jugger-api-route/' + this.selected, {
                    mode: 'cors',
                    method: 'delete',
                    headers: {
                        'Authorization': 'Bearer '  + this.$session.get('accessToken'),
                        "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                        "Accept": "application/json"
                    }
                })
                    .then(this.httpCode)
                    .then(this.handleDelete)
                    .catch(this.genericError);
            },
            handleDelete(json) {
                this.isLoading = false;
                this.handle(json, this.deleteSuccess, this.handleAllErrors);
            },
            deleteSuccess(json) {
                $('#deleteModal').modal('hide');
                this.addMessage(true, 'Successfully Deleted');
                this.resetSelected();
                this.fetchList();
            },
            doSearch() {
                this.fetchList();
            }
        },
        mounted() {
            if (!this.$session.exists()) {
                window.location = this.rootUrl + '/jugger-api';
            }

            $('[data-toggle="tooltip"]').tooltip();

            this.models = JSON.parse(this.propModels);
            this.versions = JSON.parse(this.propVersions);

            this.fetchList();
        },
        watch: {
            selectedVersion(val) {
                this.fetchList();
            }
        }
    }
</script>

<style scoped>
    th, td {
        text-align: center;
    }
    td ul li {
        text-align: left;
    }
    td {
        overflow: auto;
    }
    .breadcrumb-parent {
        display:inline-block; width: auto;
    }
</style>