@extends('layouts.admin')

@section('content')
  <section class="startmatch-wrap dashboard-page">
    <div class="container">
      <div class="row">
        <div class="col-12">
          <div class="organizer-info">
            <form action="{{ route('venues-admin.store') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group enter-members p-0">
                <label for="section-count">Enter the number of venues:</label>
                <input type="number" class="form-control" id="section-count" min="1" placeholder="Enter a number">
              </div>
              <div id="sections-container">
                <!-- Default Section without Delete Option -->
                <div class="section membersSection mb-3" id="section-1">
                  <h5>Venue 1</h5>
                  <div class="form-group">
                    <label for="name-1">Venue Name:</label>
                    <input type="text" class="form-control" name="venues[0][name]" id="name-1" placeholder="Enter name" Required>
                  </div>
                  <div class="form-group">
                    <label for="location-1">Location:</label>
                    <input type="text" class="form-control" name="venues[0][location]" id="location-1" placeholder="Enter location" Required>
                  </div>
                  <div class="form-group">
                    <label for="image-1">Venue Image:</label>
                    <input type="file" class="form-control" name="venues[0][image]" id="image-1" accept=".jpg,.jpeg,.png,.svg">
                  </div>
                </div>
              </div>
              <div class="col-12 pagination-wrap">
                <button type="submit" class="btn btn-primary text-capitalize submit-btn">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
      $(document).ready(function() {
          function createSection(index) {
              return `
                  <div class="section mb-3 membersSection" id="section-${index}">
                      <h5>Venue ${index}</h5>
                      <div class="form-group">
                          <label for="name-${index}">Venue Name:</label>
                          <input type="text" class="form-control" name="venues[${index-1}][name]" id="name-${index}" placeholder="Enter the venue name">
                      </div>
                      <div class="form-group">
                          <label for="location-${index}">Location:</label>
                          <input type="text" class="form-control" name="venues[${index-1}][location]" id="location-${index}" placeholder="Enter the location">
                      </div>
                      <div class="form-group">
                          <label for="image-${index}">Venue Image:</label>
                          <input type="file" class="form-control" name="venues[${index-1}][image]" id="image-${index}" accept="image/*">
                      </div>
                      <div class="remove-section"><button type="button" class="btn btn-danger" data-index="${index}">Delete Section</button></div>
                  </div>
              `;
          }

          $('#section-count').on('input', function() {
              const count = parseInt($(this).val()) || 0;
              const currentSections = $('#sections-container .section').length;

              if (count > currentSections) {
                  for (let i = currentSections + 1; i <= count; i++) {
                      $('#sections-container').append(createSection(i));
                  }
              } else {
                  for (let i = currentSections; i > count; i--) {
                      $(`#section-${i}`).remove();
                  }
              }
          });

          $(document).on('click', '.remove-section', function() {
              const index = $(this).data('index');
              $(`#section-${index}`).remove();

              $('#sections-container .section').each(function(i) {
                  const newIndex = i + 1;
                  $(this).attr('id', `section-${newIndex}`);
                  $(this).find('h5').text(`Venue ${newIndex}`);
                  $(this).find('.remove-section').data('index', newIndex);
                  $(this).find('input').each(function() {
                      const name = $(this).attr('id').split('-')[0];
                      $(this).attr('id', `${name}-${newIndex}`);
                      $(this).attr('name', `venues[${newIndex-1}][${name}]`);
                  });
              });

              $('#section-count').val($('#sections-container .section').length);
          });

          $('#section-count').val(1);
      });
  </script>
@endsection
