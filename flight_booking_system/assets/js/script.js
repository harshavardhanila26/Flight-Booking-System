// /assets/js/script.js

// Get the modal element
const modal = document.getElementById("schedulesModal");

// Function to open the modal and fetch schedules
async function viewSchedules(routeId) {
    const contentDiv = document.getElementById("schedulesContent");
    contentDiv.innerHTML = "<p>Loading available dates...</p>";
    modal.style.display = "block";

    try {
        const response = await fetch(`actions/get_schedules_action.php?route_id=${routeId}`);
        const schedules = await response.json();
        
        if (schedules.error) {
            contentDiv.innerHTML = `<p class="error-msg">${schedules.error}</p>`;
            return;
        }

        if (schedules.length === 0) {
            contentDiv.innerHTML = "<p>No scheduled dates found for this route.</p>";
            return;
        }

        let html = `
            <h3>Select Date and Class</h3>
            <p style="font-style: italic; color: #555;"><strong>Note:</strong> Window seats incur an additional charge of ₹1000.</p>
            <table>
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>3rd Class</th>
                        <th>2nd Class</th>
                        <th>1st Class</th>
                        <th>Book</th>
                    </tr>
                </thead>
                <tbody>
        `;

        schedules.forEach(sch => {
            // Calculate prices for each class
            const price3rd = parseFloat(sch.base_price);
            const price2nd = price3rd * 1.5;
            const price1st = price3rd * 2.5;

            html += `
                <tr>
                    <td>${new Date(sch.departure_datetime).toLocaleString()}</td>
                    <td>₹${price3rd.toFixed(2)}</td>
                    <td>₹${price2nd.toFixed(2)}</td>
                    <td>₹${price1st.toFixed(2)}</td>
                    <td>
                        <form action="actions/book_flight_action.php" method="POST">
                            <input type="hidden" name="schedule_id" value="${sch.schedule_id}">
                            <div style="display:flex; gap: 5px;">
                                <select name="seat_class" required style="padding: 5px;">
                                    <option value="3rd Class">3rd Class</option>
                                    <option value="2nd Class">2nd Class</option>
                                    <option value="1st Class">1st Class</option>
                                </select>
                                <select name="seat_type" required style="padding: 5px;">
                                    <option value="Normal">Normal</option>
                                    <option value="Window">Window</option>
                                </select>
                                <button type="submit" class="btn" style="width:auto; padding: 5px 10px;">Book</button>
                            </div>
                        </form>
                    </td>
                </tr>
            `;
        });

        html += `</tbody></table>`;
        contentDiv.innerHTML = html;

    } catch (error) {
        contentDiv.innerHTML = `<p class="error-msg">Failed to load data. ${error}</p>`;
    }
}

// Function to close the modal
function closeModal() {
    modal.style.display = "none";
}

// Close the modal if the user clicks anywhere outside of the modal content
window.onclick = function(event) {
    if (event.target == modal) {
        closeModal();
    }
}