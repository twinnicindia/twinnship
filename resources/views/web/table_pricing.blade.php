<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Home</title>
    @include('web.pages.styles')
</head>

<body>

    @include('web.pages.header')

    <section class="hero-section page-title-area">
        <div class="container">
            <div class="page-title-content">
                <h2>Pricing</h2>
            </div>
        </div>
        <div class="lines">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
    </section>

    <section class="info-section section-padding">
        <div class="container">
            <div class="section-title">
                <h3 style="color:#1874C1;">Shipping Charges(Rates in INR)</h3>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="font-size:18px;color:#1874C1;">Courier Partners</th>
                                            <th style="font-size:18px;color:#1874C1;">Zone A</th>
                                            <th style="font-size:18px;color:#1874C1;">Zone B</th>
                                            <th style="font-size:18px;color:#1874C1;">Zone C</th>
                                            <th style="font-size:18px;color:#1874C1;">Zone D</th>
                                            <th style="font-size:18px;color:#1874C1;">Zone E</th>
                                       
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">Xpress Bees</td>
                                            <td>30</td>
                                            <td>38</td>
                                            <td>42</td>
                                            <td>48</td>
                                            <td>55</td>
                                          
                                        </tr>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">Delhivery</td>
                                            <td>32</td>
                                            <td>40</td>
                                            <td>44</td>
                                            <td>50</td>
                                            <td>60</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">DTDC</td>
                                            <td>30</td>
                                            <td>36</td>
                                            <td>46</td>
                                            <td>52</td>
                                            <td>65</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">Wow Express</td>
                                            <td>29</td>
                                            <td>37</td>
                                            <td>41</td>
                                            <td>47</td>
                                            <td>60</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">Shadow Fax</td>
                                            <td>29</td>
                                            <td>37</td>
                                            <td>41</td>
                                            <td>47</td>
                                            <td>60</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">Fedex</td>
                                            <td>30</td>
                                            <td>35</td>
                                            <td>45</td>
                                            <td>52</td>
                                            <td>68</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:18px;color:#1874C1;">Udaan</td>
                                            <td>30</td>
                                            <td>33</td>
                                            <td>42</td>
                                            <td>48</td>
                                            <td>58</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    @include('web.pages.footer')

    @include('web.pages.scripts')

    <script>
        $('.hero-carousel').owlCarousel({
            loop: true,
            margin: 0,
            // nav:true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
    </script>
    <script>
        $('.ease-carousel').owlCarousel({
            loop: true,
            margin: 0,
            dots: true,
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 1
                },
                1000: {
                    items: 1
                }
            }
        })
    </script>
    <script>
        AOS.init({
            disable: 'mobile'
        });
    </script>

</body>

</html>