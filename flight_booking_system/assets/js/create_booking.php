// /assets/js/script.js

// Function to open the modal and fetch schedules
function viewSchedules(routeId) {
    const modal = document.getElementById('schedulesModal');
    const contentDiv = document.getElementById('schedulesContent');
    contentDiv.innerHTML = '<p>Loading available dates...</p>';
    modal.style.display = 'block';

    fetch(`actions/get_schedules.php?route_id=${routeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                contentDiv.innerHTML = `<p style="color: red;">Error: ${data.error}</p>`;
                return;
            }
            if (data.length === 0) {
                contentDiv.innerHTML = '<p>No scheduled flights available for this route at the moment.</p>';
                return;
            }

            let html = '<table><thead><tr><th>Departure</th><th>Price (Economy)</th><th>Seats Left</th><th>Class & Seat</th><th>Action</th></tr></thead><tbody>';
            
            data.forEach(schedule => {
                const departure = new Date(schedule.departure_datetime);
                const formattedDate = departure.toLocaleDateString('en-IN', { year: 'numeric', month: 'long', day: 'numeric' });
                const formattedTime = departure.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true });

                html += `
                    <tr>
                        <form action="actions/create_booking.php" method="POST">
                            <td>${formattedDate}<br>${formattedTime}</td>
                            <td>₹${parseFloat(schedule.base_price).toFixed(2)}</td>
                            <td>${schedule.available_seats}</td>
                            <td>
                                <input type="hidden" name="schedule_id" value="${schedule.schedule_id}">
                                <div class="form-group" style="margin-bottom: 5px;">
                                    <select name="seat_class" required>
                                        <option value="Economy">Economy</option>
                                        <option value="Business">Business (+80%)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select name="seat_type" required>
                                        <option value="Aisle">Aisle</option>
                                        <option value="Window">Window (+₹1000)</option>
                                    </select>
                                </div>
                            </td>
                            <td><button type="submit" class="btn">Book Now</button></td>
                        </form>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            contentDiv.innerHTML = html;
        })
        .catch(error => {
            contentDiv.innerHTML = `<p style="color: red;">Failed to load flight schedules. Please try again later.</p>`;
            console.error('Error fetching schedules:', error);
        });
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('schedulesModal');
    modal.style.display = 'none';
}

// Close modal if user clicks outside of the content area
window.onclick = function(event) {
    const modal = document.getElementById('schedulesModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}