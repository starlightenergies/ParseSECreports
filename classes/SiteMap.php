<?php
declare(strict_types=1);
namespace JDapp\Classes;

/* MIT LICENSE
Copyright 2021 StarlightEnergies.com
Permission is hereby granted, free of charge, to any person obtaining a copy of this software
and associated documentation files (the "Software"), to deal in the Software without restriction,
including without limitation the rights to use, copy, modify, merge, publish, distribute,
sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH
THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/**
 * @purpose:    	XBRL Report Processing Application
 * @filename:    	SiteMap
 * @version:    	1.0
 * @lastUpdate:  	2021-06-29
 * @author:        	James Danforth <james@reemotex.com>
 * @pattern:
 * @since:    		2021-06-29
 * @controller:
 * @view:
 * @mytodo:
 * @delegates:
 * @inheritsFrom:
 * @inheritsTo:
 * @comment:		template for later use
 */


class Sitemap {


		public $webPageRefs = ['Homepage:'     		   => 'Overview',						//home page
									'Login:in'		       => 'Login Page',						//main menu item
									'Register:'		       => 'Register Page',					//main menu item
                                    'Swiftapps:'                => 'Apple Apps',             		//main menu item
                                    'Titanium:'                 => 'Strategic Metal',        		//main menu item
                                    'Thermo:'                   => 'Science Root',           		//main menu item
                                    'Sodium:'                   => 'Spicy',                  		//main menu item
                                    'Silicon:'                  => 'Pervasive',             		//main menu item
                                    'Physics:'                  => 'Science Root',          		//main menu item
                                    'Nickel:'                   => 'Low Cost High Value',   		//main menu item
                                    'Matlab:'                   => 'Incredible Tool',        		//main menu item
                                    'Mathstart:'                => 'Science Root',          		//main menu item not
                                    'Mathematics:'              => 'Root of All',               	//main menu item
                                    'Investing:'                => 'Be Careful',            		//main menu item
                                    'Chemistry:'                => 'Science Root',          		//main menu item
                                    'PHPapps:'                  => 'PHP-Linux Apps',        		//main menu item
                                    'Admin:'	                => 'Admin Dashboard',       		//main menu item
                                    'Shortsale:'                => 'Do your homework',      		//parent menu child 1
                                    'Fitness:'                  => 'For life',              		//main menu item
                                    'Health:'                   => 'Key to life',            		//main menu item
                                    'Lifestyle:'                => 'Key to fun',            		//main menu item
                                    'Nutrition:'                => 'Provides the fuel',      		//main menu item
                                    'Travel:'                   => 'Builds happiness',       		//main menu item
                                    'Careers:'                  => 'Do what you love',       		//main menu item
                                    'LibrarySystem:'            => 'Research Centers ...',
 									'WatchListShorts:'			=> 'Watch Relative Performance', 		//main menu
									'WatchListHomePage:'		=> 'Watch First...',					//main menu WLHP
									'LongWatchList:'			=> 'Dont sell too soon', 				//child level 1 to WLHP 1
									'ShortWatchList:'			=> 'Better to watch than engage', 		//child level 1 to WLHP 2
									'WatchStockList:'			=> 'Lists show investor trends', 		//child level 1 to WLHP 3
									'WatchList:'				=> 'Sometimes nothing more to watch :-)',	//child level 1 to WLHP 4
									'AntiClimateWatchList:'		=> 'In the path of damage',				//child level 1 to WLHP 5
									'OilStockWatchList:'		=> 'Just plain evil',					//child level 1 to WLHP 6
									'IpoStockWatch:'			=> 'Most are dumb',						//child level 1 to WLHP 7
									'UtilityStockWatch:'		=> 'Entrenched and boring',				//child level 1 to WLHP 8
									'AutoFossilsWatch:'			=> 'Status Quo Seekers',				//child level 1 to WLHP 9
									'CommunicationsWatch:'		=> 'Beware SpaceX ...',					//child level 1 to WLHP 10
									'Tech1StockWatch:'			=> 'Tech is Secular Trend',				//child level 1 to WLHP 11
									'Tech2StockWatch:'			=> 'Tech is Secular Trend',				//child level 1 to WLHP 12
									'RealEstateStockWatch:'		=> 'Location is Key',				    //child level 1 to WLHP 13
									'SolarRenewablesWatch:'		=> 'Infinitely available always',		//child level 1 to WLHP 14
									'CyclicalStockWatch:'		=> 'Buy Low Sell High but Dont Hold',	//child level 1 to WLHP 15
									'CloudStockList:'			=> 'Plenty Room For Growth',			//child level 1 to WLHP 16
									'HealthStockWatch:'			=> 'Plenty Room For Improvement',		//child level 1 to WLHP 17
									'FrackerStockWatch:'		=> 'Ruining Fresh Water for Profit',	//child level 1 to WLHP 18
									'Consumer1WatchList:'		=> 'Consumers Drive the Economy',		//child level 1 to WLHP 19
									'Consumer2WatchList:'		=> 'Consumers Always Need Jobs',		//child level 1 to WLHP 20
									'DefenseStocksWatchList:'	=> 'A Necessary Evil',					//child level 1 to WLHP 21
									'FinancialStockWatch:'		=> 'Beneficial But Too Expensive',		//child level 1 to WLHP 22
									'RetailStockWatch:'			=> 'Some Add Value',					//child level 1 to WLHP 23
									'DisruptorStocksWatch:'		=> 'Truly Best Investments',			//child level 1 to WLHP 24
									'MarketDinosaursWatch:'		=> 'De-Facto Gov\'t Agencies',			//child level 1 to WLHP 25
									'Dashboard:'				=> 'Much to do...',						//Dashboard Main Menu
									'SystemGuide:'				=> 'Pretty easy to learn',				//child level 1 to Dashboard
									'AddMember:'				=> 'One by One',						//child level 1 to Dashboard
									'DocumentCategories:'		=> 'Just The Beginning',				//child level 1 to Dashboard
									'AutomatedDocumentCreator:'	=> 'Document Creation Wizards',  			//child level 1 to Dashboard
									'UserPreferences:'			=> 'Easy To Customize',					//child level 1 to Dashboard
									'CarouselSystem:'			=> 'Job Carousel',						//child level 1 to Dashboard
									'MessagingSystem:'	        => 'Messaging-inbox',						//child level 1 to Dashboard
									'MessagingSystemOutbox:'	=> 'Messaging-outbox',						//child level 2 to Dashboard
									'MessagingSystemSend:'		=> 'Messaging-send',						//child level 2 to Dashboard
									'MessagingSystemUsers:'		=> 'Messaging-users',						//child level 2 to Dashboard
									'MessagingSystemEmail:'		=> 'Messaging-email-out',		//child level 2 to Dashboard
									'MessagingSystemNews:'		=> 'Messaging-news',			//child level 2 to Dashboard
									'MessagingSystemAds:'		=> 'Messaging-advertising',		//child level 2 to Dashboard
									'MessagingSystemTerm:'  	=> 'An experimental idea',
									'Solutions:'				=> 'Make Life Better',
									'Engines:'					=> 'Secret Formulas Here',
									'Messaging:'				=> 'Communications are like glue',
									'MessagingSystemEmailOut:'	=> 'Messaging-email-in',				//child level 2 to Dashboard
									'Teams:'					=> 'Create, Identify, Solve',
									'TesterA:'					=> 'Testing page',
									'LessonWizard:'				=> 'Step 1 - Structure & Id',
									'LessonWizardStep2:'		=> 'Step 2 - Presentation',
									'LessonWizardStep3:'		=> 'Step 3P - Upload or Create',
									'LessonWizardEmailSender:'	=> 'Step 4 - Send Lesson',
									'LessonWizardStep3H:'		=> 'Step 3H - Upload or Create',
									'LessonWizardEmailConfirm:'	=> 'Email sent and stored',
									'Quickmail:'				=> 'Testing Email',
									'FirewallManager:'			=> 'Reduces Cost of Electricity',
									'CrontabManager:'			=> 'Handy Automate Tool',
			];


}