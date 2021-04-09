import org.junit.After;
import org.junit.Assert;
import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;

import java.util.concurrent.TimeUnit;


/**
 * Tests all actions in forum
 * The internal classes represent an action or "When"
 * Tests are labeled as such: _ID_Given_Should();
 */
public class Test_ForumPage {
    WebDriver driver;

    //region getters
    /**
     * @return the username input field
     */
    private WebElement getUsernameInput(){
        return driver.findElement(By.name("userid"));
    }

    /**
     * @return the password input field
     */
    private WebElement getPasswordInput(){
        return driver.findElement(By.name("pass"));
    }

    /**
     * @return the submit button. Used to submit login form
     */
    private WebElement getSubmitBtn(){
        return driver.findElement(By.name("submitted"));
    }

    /**
     * @return The element containing the error message
     */
    private WebElement getErrorMessage(){
        return driver.findElement(By.className("error"));
    }
    //endregion

    /**
     * Before doing any actions, the user must be logged in first
     */
    public void login(){
        this.getUsernameInput().sendKeys("AvdoulosA1");
        this.getPasswordInput().sendKeys("17c4badeE");
        this.getSubmitBtn().submit();
    }

    @Before
    public void setUp(){
        System.setProperty("webdriver.chrome.driver", "resources/windows/chromedriver.exe");
        driver = new ChromeDriver();
        driver.get(GLOBAL.rootUrl+"mbforum.php");
    }

    //region PostComment

        //region getters

        /**
         * @return the button when clicked, expands comments
         */
        private WebElement getCommentsButton(){
            return driver.findElement(By.className("getComments"));
        }

        /**
         * @return the comment input field
         */
        private WebElement getCommentInput(){
            return driver.findElement(By.name("comment"));
        }

        /**
         * @return the button that submits the comment
         */
        private WebElement getPostCommentBtn(){
            return driver.findElement(By.id("oneButton"));
        }

        /**
         * @return element that indicates if a comment was sent
         */
        private WebElement getCommentSentStatus(){
            return driver.findElement(By.xpath("//*[@id=\"main\"]/p"));
        }

    //endregion

        @Test
        public void _6_GivenUserComment_ShouldDisplayMessage(){
            login();
            getCommentsButton().click();
            getCommentInput().sendKeys("This is a test comment.");
            getPostCommentBtn().click();

            driver.manage().timeouts().implicitlyWait(15, TimeUnit.SECONDS);

            Assert.assertTrue(this.getCommentSentStatus().getText().trim().length() > 0);
        }

    //endregion

    //region ViewMessages
        @Test
        public void _7_WhenLoggedIn_ViewComments(){
            login();
            getCommentsButton().click();
        }

        @Test
        public void _8_WhenNotLoggedIn_DoNotViewComments(){
            try{
                this.getCommentsButton();
            }catch (org.openqa.selenium.NoSuchElementException e){
                Assert.assertTrue(true);
            }
        }

    //endregion

    //region Logout
        //region getters

        /**
         * @return the button that logs a user out
         */
        private WebElement getLogoutBtn(){
            return driver.findElement(By.xpath("//*[@id=\"login\"]/a[1]"));
        }

        //endregion

        @Test
        public void _5_WhenUserIsLoggedOut_CannotInteractWithForum(){
            login();
            this.getLogoutBtn().click();
            _8_WhenNotLoggedIn_DoNotViewComments();
        }

    //endregion

    @After
    public void tearDown(){
        driver.quit();
    }
}
