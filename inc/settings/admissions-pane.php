<!--Admission Pane-->
<div class="tab-pane fade" id="admission-tab-pane" role="tabpanel" aria-labelledby="admission-tab" tabindex="0"><!--Programmes Pane-->
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-1">
            </div>
            <div class="col-lg-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Start Date</th>
                            <th scope="col">End Date</th>
                            <th scope="col">Description</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ad_p = $admin->fetchAllAdmissionPeriod();
                        if (!empty($ad_p)) {
                            foreach ($ad_p as $ad) {
                        ?>
                                <tr>
                                    <td><?= $ad["start_date"] ?></td>
                                    <td><?= $ad["end_date"] ?></td>
                                    <td><?= $ad["info"] ?></td>
                                    <td>
                                        <?php if ($ad["active"]) { ?>
                                            <span id="<?= $ad["id"] ?>" style="cursor:pointer;" class="edit-adp bi bi-pencil-square text-primary" title="Edit admission period"></span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($ad["active"]) { ?>
                                            <button id="<?= $ad["id"] ?>" class="btn btn-danger btn-sm">
                                                <span style="cursor:pointer;" class="bi bi-door-closed text-default" title="Edit admission period"></span> Close
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                        <?php }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="col-lg-1">
            </div>

            <div class="col-lg-4">
                <form id="addOrUpdateAdmisPeriodForm" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <h5 class="card-header">Set New Admission Period</h5>
                        <div class="card-body">
                            <div style="display: flex; flex-direction:row; justify-content: space-between">
                                <div class="mb-2 me-2">
                                    <label for="adp-start">Start Date</label>
                                    <input type="date" name="adp-start" id="adp-start" class="form-control form-control-sm">
                                </div>
                                <div class="mb-2">
                                    <label for="adp-end">End Date</label>
                                    <input type="date" name="adp-end" id="adp-end" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="adp-desc">Description</label>
                                <input type="text" class="form-control form-control-sm" name="adp-desc" id="adp-desc" placeholder="Description">
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm" id="adp-action-btn">Open</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="adp-action" id="adp-action" value="add">
                    <input type="hidden" name="adp-id" id="adp-id" value="">
                </form>
            </div>

            <!-- Add form type modal form-->
            <div class="modal fade" id="addFormType" tabindex="-1" aria-labelledby="addFormTypeLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Type</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formTypeForm" action="#" method="post" class="">
                                <div class="card">
                                    <h5 class="card-header">Add Form</h5>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <label for="form-name">Action</label>
                                            <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                    <option value="add">Add</option>
                                                    <option value="Update">Update</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="form-price">Form Name</label>
                                            <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Understood</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function resetADPForm() {
            $("#adp-id").val("");
            $("#adp-desc").val("");
        }

        $("#addOrUpdateAdmisPeriodForm").on("submit", function(e) {
            e.preventDefault();
            alert("ADD")

            if ($("#adp-action").val() == "add") {
                alert("ADD")
            }

            $.ajax({
                type: "POST",
                url: "../endpoint/adp-form",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        alert(result.message);
                        resetADPForm();
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        $(".edit-adp").click(function(e) {
            let data = {
                adp_key: $(this).attr("id")
            }

            $.ajax({
                type: "GET",
                url: "../endpoint/adp-form",
                data: data,
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        $("#adp-action").attr("value", "update");
                        $(".card-header").text("Update Programme");
                        $("#adp-action-btn").text("Update");
                        $("#adp-id").val(result.message[0].id);
                        $("#adp-start").val(result.message[0].start_date);
                        $("#adp-end").val(result.message[0].end_date);
                        $("#adp-desc").val(result.message[0].info);
                    } else {
                        alert(result.message)
                    };

                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        $(".close-adp").click(function(e) {
            alert($(this).attr("id"))
            var data = {
                adp_key: $(this).attr("id")
            }

            $.ajax({
                type: "PUT",
                url: "../endpoint/adp-form",
                data: data,
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        alert(result.message);
                        resetADPForm();
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    });
</script>