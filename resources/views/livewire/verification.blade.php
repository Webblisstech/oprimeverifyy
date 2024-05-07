<div wire:poll>
    <div class="table-responsive ">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>Phone No</th>
                    <th>Code</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>


                </tr>
            </thead>
            <tbody>

                @forelse($verification as $data)
                <tr>
                    <td style="font-size: 12px;">{{ $data->id }}</td>
                    <td style="font-size: 12px;">{{ $data->service }}</td>
                    <td style="font-size: 12px; color: green"><a href="receive-sms?phone={{ $data->id }}">{{ $data->phone }} </a></td>
                    <td style="font-size: 12px;">{{ $message }}</td>
                    <td style="font-size: 12px;">₦{{ number_format($data->cost, 2) }}</td>
                    <td>
                        @if ($data->status == 1)
                        <span style="background: orange; border:0px; font-size: 10px" class="btn btn-warning btn-sm">Pending</span>
                        @if($data->type == 'tella')
                        <a href="cancle-tella-sms?id={{ $data->order_id }}&delete=1" style="background: rgb(168, 0, 14); border:0px; font-size: 10px" class="btn btn-warning btn-sm">Delete</span>
                            @else
                            <a href="cancle-sms?id={{  $data->id }}&delete=1" style="background: rgb(168, 0, 14); border:0px; font-size: 10px" class="btn btn-warning btn-sm">Delete</span>
                                @endif
                                @else
                                <span style="font-size: 10px;" class="text-white btn btn-success btn-sm">Completed</span>
                                @endif

                    </td>
                    <td style="font-size: 12px;">{{ $data->created_at }}</td>
                </tr>

                @empty

                <h6>No verification found</h6>
                @endforelse

            </tbody>
                {{ $verification->links() }}
        </table>
    </div>
</div>