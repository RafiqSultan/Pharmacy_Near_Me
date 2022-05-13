<div id="wait-pay" class="tab-pane fade">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">رقم الطلبية</th>
                <th scope="col">الصيدلية</th>
                <th scope="col">تاريخ الطلبية</th>
                <th scope="col">سعر الطلبية</th>
                <th scope="col">حالة الطلبية</th>
                <th scope="col">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                @if ($request->state == \App\Utils\RequestState::ACCEPTED)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $request->pharmacy->user->name }}</td>
                        <td>{{ $request->created_at->diffForHumans() }}</td>
                        <td>
                            {{ $request->replies->details->sum('drug_price') }}
                        </td>
                        <td><span class="badge bg-warning text-dark" style="background-color:rgb(240, 225, 15);">في
                                انتضار الدفع</span></td>
                        <td><a class="btn btn-primary" href="javascript:void(0);" data-bs-toggle="collapse" role="button"
                                data-bs-target="#details{{ $request->id }}">
                                عرض التفاصيل
                            </a></td>
                        <td><a class="btn btn-success" href="{{ route('client-payment',$request->id) }}">
                                دفع
                            </a></td>
                        <td><a href="{{ route('client-orders-reject', $request->id) }}" class="btn btn-danger">
                                رفض
                            </a></td>

                    </tr>

                    <tr>
                        <td colspan="6">
                            <div class=" collapse" id="details{{ $request->id }}">
                                <div class="card card-body">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">أسم/صورة العلاج</th>
                                                <th scope="col"> الكمية</th>
                                                <th scope="col">أقبل البدائل</th>
                                                <th scope="col">العمليات </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($request->details as $requestDetails)
                                                <tr>
                                                    <td>{{ $requestDetails->drug_title }}</td>
                                                    <td>{{ $requestDetails->quantity }}</td>
                                                    <td>
                                                        @if ($requestDetails->accept_alternative)
                                                            <span class="badge bg-success text-light">
                                                                نعم
                                                            @else
                                                                <span class="badge bg-danger text-light">
                                                                    لا
                                                        @endif
                                                        </span>
                                                    </td>
                                                    <td><a class="btn btn-primary" href="javascript:void(0);"
                                                            data-bs-toggle="collapse" role="button"
                                                            data-bs-target="#reply{{ $requestDetails->id }}">
                                                            عرض الرد
                                                        </a></td>
                                                </tr>

                                                <tr>
                                                    <td colspan="5">
                                                        <div class=" collapse"
                                                            id="reply{{ $requestDetails->id }}">
                                                            <div class="card card-body">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">أسم/صورة العلاج</th>
                                                                            <th scope="col"> الكمية</th>
                                                                            <th scope="col">نوع الرد</th>
                                                                            <th scope="col">سعر العلاج </th>
                                                                            <th scope="col">قبول الرد </th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($request->replies->details as $replyDetails)
                                                                            @if ($requestDetails->id == $replyDetails->request_details_id)
                                                                                <tr>
                                                                                    @if ($replyDetails->drug_price)
                                                                                        <td>{{ $requestDetails->drug_title }}
                                                                                        </td>
                                                                                        <td>{{ $requestDetails->quantity }}
                                                                                        </td>
                                                                                        <td><span
                                                                                                class="badge bg-primary text-light">اساسي</span>
                                                                                        </td>
                                                                                        <td>{{ $replyDetails->drug_price }}
                                                                                        </td>
                                                                                    @else
                                                                                        <td>{{ $replyDetails->alt_drug_title }}
                                                                                        </td>
                                                                                        <td>{{ $requestDetails->quantity }}
                                                                                        </td>
                                                                                        <td><span
                                                                                                class="badge bg-secondary text-light">بديل</span>
                                                                                        </td>
                                                                                        <td>{{ $replyDetails->alt_drug_price }}
                                                                                        </td>
                                                                                    @endif


                                                                                    <td>
                                                                                        <input type="checkbox" name=""
                                                                                            data-id="{{ $replyDetails->id }}"
                                                                                            class="replyDetailsState"
                                                                                            @if ($replyDetails->state == \App\Utils\ReplyState::ACCEPTED) checked @endif>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"
integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    $(".replyDetailsState").change(function() {
        var state = "{{ \App\Utils\ReplyState::REJECTED }}";
        if (this.checked)
            state = "{{ \App\Utils\ReplyState::ACCEPTED }}";

        var url = "/client/reply-details/" + $(this).attr('data-id') + "/toggle/" + state;

        $.ajax({
            method: 'get',
            url: url,
            success: function(data) {
                alert(data);
            }
        });
    });
</script>
